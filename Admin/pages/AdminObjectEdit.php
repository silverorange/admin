<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'Admin/pages/AdminDBEdit.php';

/**
 * Admin edit page for SwatDBDataObjects
 *
 * @package   Admin
 * @copyright 2014 silverorange
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
	 * An array of SwatDBDataObject objects to flush
	 *
	 * @var array
	 */
	protected $data_objects_to_flush = array();

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
	}

	// }}}
	// {{{ protected function initObject()

	protected function initObject()
	{
		$class_name = $this->getResolvedObjectClass();
		$this->data_object = new $class_name();
		$this->data_object->setDatabase($this->app->db);

		if ($this->app->hasModule('SiteMemcacheModule')) {
			$this->data_object->setFlushableCache(
				$this->app->getModule('SiteMemcacheModule')
			);
		}

		if (!$this->isNew() && !$this->data_object->load($this->id)) {
			throw new AdminNotFoundException(
				sprintf(
					'A %s with the id of ‘%s’ does not exist',
					$class_name,
					$this->id
				)
			);
		}
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

		// If the main data object wasn't modified, don't flush any other
		// data objects automagically.
		if (!$this->getObject()->isModified()) {
			$this->clearObjectsToFlush();
		}

		$this->saveObject();
		$this->postSaveObject();
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

		if ($this->isNew()) {
			if ($object->hasPublicProperty('createdate') &&
				$object->hasDateProperty('createdate')) {
				$object->createdate = new SwatDate();
				$object->createdate->toUTC();
			}

			if ($object->hasPublicProperty('shortname') &&
				$this->ui->hasWidget('shortname') &&
				$this->ui->hasWidget('title')) {
				$shortname_widget = $this->ui->getWidget('shortname');

				if ($shortname_widget->value == '') {
					$shortname_widget->value = $this->generateShortname(
						$this->ui->getWidget('title')->value
					);
				}
			}
		} else {
			$old_object = clone $object;
			$this->addObjectToFlushOnSave($old_object);
		}

		return $object;
		// Subclass handles the rest.
	}

	// }}}
	// {{{ protected function saveObject()

	protected function saveObject()
	{
		$this->getObject()->save();
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
		$message_text      = $this->getSavedMessageText();
		$secondary_content = $this->getSavedMessageSecondaryContent();
		$content_type      = $this->getSavedMessageContentType();

		if ($message_text != '') {
			$message = new SwatMessage($message_text, $message_type);

			if ($secondary_text != '') {
				$message->secondary_content = $secondary_content;
			}

			if ($content_type != '') {
				$message->content_type = $content_type;
			}
		}

		return $message;
	}

	// }}}
	// {{{ protected function getSavedMessageText()

	protected function getSavedMessageText()
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
		SwatDBDataObject $object, array $names)
	{
		foreach ($names as $name) {
			$this->assignUiValueToObject($object, $name);
		}
	}

	// }}}
	// {{{ protected function assignUiValueToObject()

	protected function assignUiValueToObject(SwatDBDataObject $object,
		$name)
	{
		$widget = $this->ui->getWidget($name);

		// only clone the value when its actually an object
		if ($widget instanceof SwatDateEntry &&
			$widget->value instanceof SwatDate) {
			$value = clone $widget->value;
			$value->setTZ($this->app->default_time_zone);
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
		SwatDBDataObject $object, array $names)
	{
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
			$value->convertTZ($this->app->default_time_zone);
		}

		$widget->value = $value;
	}

	// }}}
}

?>
