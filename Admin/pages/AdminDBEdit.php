<?php

require_once 'Admin/pages/AdminEdit.php';
require_once 'Swat/SwatMessage.php';
require_once 'SwatDB/SwatDBTransaction.php';
require_once 'SwatDB/exceptions/SwatDBException.php';

/**
 * Generic admin database edit page
 *
 * This class is intended to be a convenience base class. For a fully custom
 * edit page, inherit directly from AdminPage instead.
 *
 * @package Admin
 * @copyright silverorange 2005
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminDBEdit extends AdminEdit
{
	// process phase
	// {{{ protected function saveData()

	protected function saveData()
	{
		try {
			$transaction = new SwatDBTransaction($this->app->db);
			$this->saveDBData();
			$transaction->commit();

		} catch (SwatDBException $e) {
			$transaction->rollback();

			$message = new SwatMessage(Admin::_(
				'A database error has occurred. The item was not saved.'),
				SwatMessage::SYSTEM_ERROR);

			$this->app->messages->add($message);

			$e->process();
			return false;

		} catch (SwatException $e) {
			$message = new SwatMessage(Admin::_(
				'An error has occurred. The item was not saved.'),
				SwatMessage::SYSTEM_ERROR);

			$this->app->messages->add($message);

			$e->process();
			return false;
		}
		return true;
	}

	// }}}
	// {{{ protected function saveDBData()

	/**
	 * Save the data from the database
	 *
	 * This method is called to save data from the widgets after processing.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to store the data. Widgets can be accessed through the
	 * $ui class variable.
	 */
	abstract protected function saveDBData();

	// }}}

	// build phase
	// {{{ protected function loadData()

	protected function loadData()
	{
		$this->loadDBData();
		return true;
	}

	// }}}
	// {{{ protected function loadDBData()

	/**
	 * Load the data from the database
	 *
	 * This method is called to load data to be edited into the widgets.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to obtain the data. Widgets can be accessed through the
	 * $ui class variable.
	 */
	abstract protected function loadDBData();

	// }}}
}

?>
