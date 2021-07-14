<?php

/**
 * Admin edit page for SwatDBDataObjects
 *
 * @package   Admin
 * @copyright 2014-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminObjectEdit extends AdminDBEdit
{
	// {{{ protected properties

	/**
	 * The dataobject instance we are editing on this page.
	 *
	 * @var SwatDBDataObject
	 *
	 * @see AdminObjectEdit::getObject()
	 */
	protected $data_object;

	/**
	 * When edits should replace the existing object with a new object
	 * this is the existing object being edited.
	 *
	 * @var SwatDBDataObject
	 *
	 * @see AdminObjectEdit::shouldReplaceObject()
	 */
	protected $old_object;

	/**
	 * An array of SwatDBDataObject objects to flush
	 *
	 * @var array
	 */
	protected $data_objects_to_flush = array();

	/**
	 * The current time as a SwatDate in UTC.
	 *
	 * Current time is defined on the first call of getCurrentTime() and used
	 * to return a consistent date/time when setting date fields on an edit.
	 *
	 * @var SwatDate
	 *
	 * @see AdminObjectEdit::getCurrentTime()
	 */
	protected $current_time;

	// }}}
	// {{{ abstract protected function getObjectClass()

	abstract protected function getObjectClass();

	// }}}
	// {{{ abstract protected function getUiXml()

	abstract protected function getUiXml();

	// }}}
	// {{{ protected function getResolvedObjectClass()

	protected function getResolvedObjectClass()
	{
		return SwatDBClassMap::get($this->getObjectClass());
	}

	// }}}
	// {{{ protected function getObject()

	protected function getObject()
	{
		return $this->data_object;
	}

	// }}}
	// {{{ protected function setObject()

	protected function setObject(SwatDBDataObject $object)
	{
		$this->data_object = $object;
	}

	// }}}
	// {{{ protected function getOldObject()

	protected function getOldObject()
	{
		if (!$this->old_object instanceof SwatDBDataObject) {
			$this->old_object = clone $this->getObject();
		}

		return $this->old_object;
	}

	// }}}
	// {{{ protected function shouldReplaceObject()

	/**
	 * Whether or not to replace the object being edited with a new object.
	 *
	 * This is useful for cases where generating a new object and corresponding
	 * id is necessary, for example when dealing with objects such as images
	 * that could be cached by a browser or CDN storage based on its id.
	 *
	 * @returns boolean
	 */
	protected function shouldReplaceObject()
	{
		return false;
	}

	// }}}
	// {{{ protected function getObjectUiValueNames()

	protected function getObjectUiValueNames()
	{
		return array();
	}

	// }}}
	// {{{ protected function getCurrentTime()

	protected function getCurrentTime()
	{
		if (!$this->current_time instanceof SwatDate) {
			$this->current_time = new SwatDate();
			$this->current_time->toUTC();
		}

		return $this->current_time;
	}

	// }}}
	// {{{ protected function getObjectTimeZone()

	protected function getObjectTimeZone()
	{
		$object = $this->getObject();
		$name = $this->getObjectTimeZonePropertyName();

		return property_exists($object, $name) && $object->$name != ''
			? new DateTimeZone($object->$name)
			: $this->app->default_time_zone;
	}

	// }}}
	// {{{ protected function getObjectTimeZonePropertyName()

	protected function getObjectTimeZonePropertyName()
	{
		return 'time_zone';
	}

	// }}}

	// init phase
	// {{{ public function init()

	public function init()
	{
		// Skip other admin page init methods so that we can load the UI from
		// getUiXml() as part of init.
		SitePage::init();

		$this->ui = new AdminUI();
		$this->ui->loadFromXML($this->getUiXml());

		$this->initInternal();

		$this->ui->init();
	}

	// }}}
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->initObject();
		$this->initTimeZoneWidget();
	}

	// }}}
	// {{{ protected function initObject()

	protected function initObject()
	{
		$this->data_object = $this->getNewObjectInstance();

		if (!$this->isNew()) {
			if (!$this->data_object->load($this->id)) {
				throw new AdminNotFoundException(
					sprintf(
						'A %s with the id of ‘%s’ does not exist',
						get_class($this->data_object),
						$this->id
					)
				);
			}
		}
	}

	// }}}
	// {{{ protected function initTimeZoneWidget()

	protected function initTimeZoneWidget()
	{
		$name = $this->getObjectTimeZonePropertyName();
		if ($this->ui->hasWidget($name)) {
			$widget = $this->ui->getWidget($name);
			$widget->value = $this->getObjectTimeZone()->getName();
		}
	}

	// }}}
	// {{{ protected function getNewObjectInstance()

	protected function getNewObjectInstance()
	{
		$class_name = $this->getResolvedObjectClass();
		$data_object = new $class_name();
		$data_object->setDatabase($this->app->db);

		if ($this->app->hasModule('SiteMemcacheModule')) {
			$data_object->setFlushableCache(
				$this->app->getModule('SiteMemcacheModule')
			);
		}

		return $data_object;
	}

	// }}}

	// process phase
	// {{{ protected function validateShortname()

	protected function validateShortname($shortname)
	{
		$valid = parent::validateShortname($shortname);

		$class_name = $this->getResolvedObjectClass();
		$object = new $class_name();
		$object->setDatabase($this->app->db);

		if (method_exists($object, 'loadByShortname') &&
			$object->loadByShortname($shortname) &&
			$object->id !== $this->getObject()->id) {
			$valid = false;
		}

		return $valid;
	}

	// }}}
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$this->updateObject();
		$this->updateModifiedDate();

		// If the main data object wasn't modified, don't flush any other
		// data objects automagically.
		if (!$this->getObject()->isModified()) {
			$this->clearObjectsToFlush();
		}

		$this->saveObject();
		$this->postSaveObject();
		$this->deleteOldObject();
		$this->flushObjectsOnSave();
		$this->addToSearchQueue();
		$this->addSavedMessage();

		// parent::saveDBData() expects to return true on success.
		return true;
	}

	// }}}
	// {{{ protected function updateObject()

	protected function updateObject()
	{
		$object = $this->getObject();

		// Clone the old object for flushing before any changes are made to it.
		if (!$this->isNew()) {
			$old_object = $this->getOldObject();
			$this->addObjectToFlushOnSave($old_object);
		}

		$this->assignUiValues($this->getObjectUiValueNames());

		if ($this->isNew()) {
			if ($object->hasPublicProperty('createdate') &&
				$object->hasDateProperty('createdate')) {
				$object->createdate = $this->getCurrentTime();
			}

			if ($object->hasPublicProperty('shortname') &&
				$this->ui->hasWidget('shortname') &&
				$this->ui->hasWidget('title')) {
				$shortname = $this->ui->getWidget('shortname')->value;

				if ($shortname == '') {
					$object->shortname = $this->generateShortname(
						$this->ui->getWidget('title')->value
					);
				}
			}
		} else {
			// SwatDBDataObject::duplicate makes a copy of the object with no
			// id, so a fresh row is saved. This happens at the end of
			// updateObject() so that all the values set automagically by
			// AdminObjectEdit::updateObject() get copied, but so that
			// subclasses then modify the duplicated object. It is not necessary
			// if it is a new object.
			if ($this->shouldReplaceObject()) {
				$object = $object->duplicate();
				$this->setObject($object);
			}
		}
	}

	// }}}
	// {{{ protected function updateModifiedDate()

	protected function updateModifiedDate()
	{
		$object = $this->getObject();
		if ($object->hasPublicProperty('modified_date') &&
			$object->hasDateProperty('modified_date')) {

			if ($object->isModified() ||
				$this->shouldReplaceObject() ||
				!$object->modified_date instanceof SwatDate) {
				$object->modified_date = $this->getCurrentTime();
			}
		}
	}

	// }}}
	// {{{ protected function saveObject()

	protected function saveObject()
	{
		$this->getObject()->save();
	}

	// }}}
	// {{{ protected function deleteOldObject()

	protected function deleteOldObject()
	{
		if (!$this->isNew() &&
			$this->shouldReplaceObject() &&
			$this->getOldObject() instanceof SwatDBDataObject) {
			$this->getOldObject()->delete();
		}
	}

	// }}}
	// {{{ protected function postSaveObject()

	protected function postSaveObject()
	{
	}

	// }}}
	// {{{ protected function addObjectToFlushOnSave()

	protected function addObjectToFlushOnSave(SwatDBDataObject $object)
	{
		// Make sure the flushable cache is set.
		if ($this->app->hasModule('SiteMemcacheModule')) {
			$object->setFlushableCache(
				$this->app->getModule('SiteMemcacheModule')
			);
		}

		$this->data_objects_to_flush[] = $object;
	}

	// }}}
	// {{{ protected function clearObjectsToFlush()

	protected function clearObjectsToFlush()
	{
		$this->data_objects_to_flush = array();
	}

	// }}}
	// {{{ protected function flushObjectsOnSave()

	protected function flushObjectsOnSave()
	{
		foreach ($this->data_objects_to_flush as $object) {
			if ($object instanceof SwatDBDataObject) {
				$object->flushCacheNamespaces();
			}
		}
	}

	// }}}
	// {{{ protected function addToSearchQueue()

	protected function addToSearchQueue()
	{
	}

	// }}}
	// {{{ protected function addSavedMessage()

	protected function addSavedMessage()
	{
		if ($this->app->hasModule('SiteMessagesModule')) {
			$message = $this->getSavedMessage();
			if ($message instanceof SwatMessage) {
				$this->app->getModule('SiteMessagesModule')->add($message);
			}
		}
	}

	// }}}
	// {{{ protected function getSavedMessage()

	protected function getSavedMessage()
	{
		$message = null;
		$message_type      = $this->getSavedMessageType();
		$primary_content   = $this->getSavedMessagePrimaryContent();
		$secondary_content = $this->getSavedMessageSecondaryContent();
		$content_type      = $this->getSavedMessageContentType();

		if ($primary_content != '') {
			$message = new SwatMessage($primary_content, $message_type);

			if ($secondary_content != '') {
				$message->secondary_content = $secondary_content;
			}

			if ($content_type != '') {
				$message->content_type = $content_type;
			}
		}

		return $message;
	}

	// }}}
	// {{{ protected function getSavedMessagePrimaryContent()

	protected function getSavedMessagePrimaryContent()
	{
		return null;
	}

	// }}}
	// {{{ protected function getSavedMessageSecondaryContent()

	protected function getSavedMessageSecondaryContent()
	{
		return null;
	}

	// }}}
	// {{{ protected function getSavedMessageType()

	protected function getSavedMessageType()
	{
		return null;
	}

	// }}}
	// {{{ protected function getSavedMessageContentType()

	protected function getSavedMessageContentType()
	{
		return null;
	}

	// }}}
	// {{{ protected function assignUiValues()

	protected function assignUiValues(array $names)
	{
		$this->assignUiValuesToObject($this->getObject(), $names);
	}

	// }}}
	// {{{ protected function assignUiValuesToObject()

	protected function assignUiValuesToObject(
		SwatDBDataObject $object,
		array $names
	) {
		$tz_name = $this->getObjectTimeZonePropertyName();

		if (in_array($tz_name, $names)) {
			$this->assignUiValueToObject($object, $tz_name);
		}

		foreach ($names as $name) {
			// Already processed the $tz_name property
			if ($name !== $tz_name) {
				$this->assignUiValueToObject($object, $name);
			}
		}
	}

	// }}}
	// {{{ protected function assignUiValueToObject()

	protected function assignUiValueToObject(
		SwatDBDataObject $object,
		$name
	) {
		$widget = $this->ui->getWidget($name);

		// only clone the value when its actually an object
		if ($widget instanceof SwatDateEntry &&
			$widget->value instanceof SwatDate) {
			$value = clone $widget->value;
			$value->setTZ($this->getObjectTimeZone());
			$value->toUTC();
		} else {
			$value = $widget->value;
		}

		if (property_exists($object, $name) ||
			$object->hasInternalValue($name)) {

			$object->$name = $value;
		} else {
			throw new SwatInvalidPropertyException(
				sprintf(
					'Specified “%s” object does not have a property “%s”.',
					get_class($object),
					$name
				)
			);
		}
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->loadObject();
	}

	// }}}
	// {{{ protected function loadObject()

	protected function loadObject()
	{
		$this->assignValuesToUi($this->getObjectUiValueNames());
	}

	// }}}
	// {{{ protected function assignValuesToUi()

	protected function assignValuesToUi(array $names)
	{
		$this->assignObjectValuesToUi($this->getObject(), $names);
	}

	// }}}
	// {{{ protected function assignObjectValuesToUi()

	protected function assignObjectValuesToUi(
		SwatDBDataObject $object,
		array $names
	) {
		foreach ($names as $name) {
			$this->assignObjectValueToUi($object, $name);
		}
	}

	// }}}
	// {{{ protected function assignObjectValueToUi()

	protected function assignObjectValueToUi(SwatDBDataObject $object, $name)
	{
		if (property_exists($object, $name)) {
			$value = $object->$name;
		} elseif ($object->hasInternalValue($name)) {
			$value = $object->getInternalValue($name);
		} else {
			throw new SwatInvalidPropertyException(
				sprintf(
					'Specified “%s” record does not have a property “%s”.',
					get_class($object),
					$name
				)
			);
		}

		$widget = $this->ui->getWidget($name);

		if ($widget instanceof SwatDateEntry &&
			$value instanceof SwatDate) {
			$value = new SwatDate($value);
			$value->convertTZ($this->getObjectTimeZone());
		}

		$widget->value = $value;
	}

	// }}}
}

?>
