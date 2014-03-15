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
	// {{{ protected variables

	/**
	 * @var SwatDBDataObject
	 */
	protected $data_object;

	/**
	 * @var array of SwatDBDataObject
	 */
	protected $data_objects_to_flush = array();

	// }}}
	// {{{ abstract protected function getObjectClass()

	abstract protected function getObjectClass();

	// }}}
	// {{{ abstract protected function getUiXml()

	abstract protected function getUiXml();

	// }}}
	// {{{ public function getObject()

	public function getObject()
	{
		return $this->data_object;
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML($this->getUiXml());
		$this->initObject();
	}

	// }}}
	// {{{ public function initObject()

	public function initObject()
	{
		$class_name = SwatDBClassMap::get($this->getObjectClass());
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
		$valid = true;

		$class_name = SwatDBClassMap::get($this->getObjectClass());
		$object = new $class_name();
		$object->setDatabase($this->app->db);

		if ($object->loadByShortname($shortname) &&
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
			if ($object->hasPublicProperty('createdate')) {
				$object->createdate = new SwatDate();
				$object->createdate->toUTC();
			}
		} else {
			$old_object = clone $object;
			$this->flushObjectOnSave($old_object);
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
	// {{{ protected function flushObjectOnSave()

	protected function flushObjectOnSave(SwatDBDataObject $object)
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
				$object->flushCache();
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
	}

	// }}}
	// {{{ protected function assignUiValues()

	protected function assignUiValues(array $names)
	{
		foreach ($names as $name) {
			$this->assignUiValueToObject($this->getObject(), $name);
		}
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
		if ($widget instanceof SwatDateEntry && $widget->value !== null) {
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
	// {{{ protected function assignValuesToUi()

	protected function assignValuesToUi(array $names)
	{
		foreach ($names as $name) {
			$this->assignObjectValueToUi($this->getObject(), $name);
		}
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

		if ($value !== null && $widget instanceof SwatDateEntry) {
			$value = new SwatDate($value);
			$value->convertTZ($this->app->default_time_zone);
		}

		$widget->value = $value;
	}

	// }}}
}

?>
