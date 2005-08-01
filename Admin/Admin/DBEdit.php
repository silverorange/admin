<?php

require_once 'Admin/Admin/Edit.php';
require_once 'Swat/SwatMessage.php';

/**
 * Generic admin database edit page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * edit page, inherit directly from AdminPage instead.
 *
 * @package Admin
 * @copyright silverorange 2005
 */
abstract class AdminDBEdit extends AdminEdit
{
	protected function saveData($id)
	{
		try {
			$this->app->db->beginTransaction();
			$this->saveDBData($id);
			$this->app->db->commit();

		} catch (SwatDBException $e) {
			$this->app->db->rollback();

			$msg = new SwatMessage(Admin::_('A database error has occured. The item was not saved.'), SwatMessage::ERROR);
			$this->app->addMessage($msg);

			$e->process();
			return false;

		} catch (SwatException $e) {
			$msg = new SwatMessage(Admin::_('An error has occured. The item was not saved.'), SwatMessage::ERROR);
			$this->app->addMessage($msg);

			$e->process();
			return false;
		}
		return true;
	}

	protected function loadData($id)
	{
		$this->loadDBData($id);
		return true;
	}

	/**
	 * Save the data from the database
	 *
	 * This method is called to save data from the widgets after processing.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to store the data. Widgets can be accessed through the
	 * $ui class variable.
	 *
	 * @param integer $id An integer identifier of the data to store.
	 */
	abstract protected function saveDBData($id);

	/**
	 * Load the data from the database
	 *
	 * This method is called to load data to be edited into the widgets.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to obtain the data. Widgets can be accessed through the
	 * $ui class variable.
	 *
	 * @param integer $id An integer identifier of the data to retrieve.
	 */
	abstract protected function loadDBData($id);
}

?>
