<?php

require_once 'Swat/SwatUI.php';

/**
 * UI manager for administrators
 *
 * Subclass of {@link SwatUI} for use with the Admin package.  This can be used
 * as a central place to add {@link SwatUI::$classmap class maps} and
 * {@link SwatUI::registerHandler() UI handlers} that are specific to the Admin
 * package.
 *
 * @package   Admin
 * @copyright 2004-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminUI extends SwatUI
{
	// {{{ public function getValues()

	/**
	 * Gets values from widgets
	 *
 	 * Convenience method to retrive values from multiple widgets at once.
	 * This method is useful when using {@link SwatDB::rowInsert()} and
	 * {@link SwatDB::rowUpdate} but only works if the widget id and
	 * field name are the same, if this is not the case you should manually get
	 * the values.
	 *
	 * @param array $ids an array of widget ids to retrieve values from.
	 *
	 * @return array an array of widget values indexed by widget ids.
	 */
	public function getValues(array $ids)
	{
		$values = array();

		foreach ($ids as $widget_id)
			$values[$widget_id] = $this->getWidget($widget_id)->value;

		return $values;
	}

	// }}}
	// {{{ public function setValues()

	/**
	 * Sets values of widgets
	 *
 	 * Convenience method to set values of multiple widgets at once.
	 * This method is useful when using {@link SwatDB::rowQuery()}
	 * but only works if the widget id and field name are the same, if this
	 * is not the case you should manually set the values.
	 *
	 * If a widget id-value pair is passed for a widget that does not exist,
	 * that value is ignored.
	 *
	 * @param array $values an array of widget values indexed by widget ids.
	 */
	public function setValues(array $values)
	{
		foreach ($values as $id => $value) {
			try {
				$widget = $this->getWidget($id);
				$widget->value = $values[$id];
			} catch (SwatWidgetNotFoundException $e) {
			}
		}
	}

	// }}}
}

?>
