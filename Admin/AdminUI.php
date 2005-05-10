<?php

require_once('Swat/SwatUI.php');

/**
 * UI manager for administrators
 *
 * Subclass of {@link SwatUI} for use with the Admin package.  This can be used
 * as a central place to add {@link SwatUI::$classmap class maps} and 
 * {@link SwatUI::registerHandler() UI handlers} that are specific to the Admin 
 * package.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminUI extends SwatUI {

	/**
	 * Create a new AdminUI object
	 */
	public function __construct() {
		parent::__construct();

		$this->classmap = array('Admin' => 'Admin');
	}

	/**
	 * Get values from widgets
	 *
 	 * Convenience method to retrive values from multiple widgets at once.
	 * This method is useful when using {@link SwatDB::rowInsert()} and
	 * {@link SwatDB::rowUpdate} but only works if the widget id and
	 * field name are the same, if this is not the case you should manually get
	 * the values.
	 *
	 * @return array Array of values with widget ids as the keys.
	 * @param array $ids Array of widget ids to retrieve values from.
	 */
	public function getValues($ids) {
		$values = array();

		foreach ($ids as $widget_id)
			$values[$widget_id] = $this->getWidget($widget_id)->value;

		return $values;
	}

	/**
	 * Set values of widgets
	 *
 	 * Convenience method to set values of multiple widgets at once.
	 * This method is useful when using {@link SwatDB::rowQuery()}
	 * but only works if the widget id and field name are the same, if this
	 * is not the case you should manually set the values.
	 *
	 * @param array $values Array of values with widget ids as the keys.
	 */
	public function setValues($values) {
		foreach ($values as $id => $value) {
			$widget = $this->getWidget($id, true);

			if ($widget !== null)
				$widget->value = $values[$id];
		}
	}
}

?>
