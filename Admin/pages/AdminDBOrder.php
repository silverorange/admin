<?php

require_once 'Admin/pages/AdminOrder.php';
require_once 'SwatDB/SwatDBTransaction.php';

/**
 * DB admin ordering page
 *
 * An ordering page with DB error checking.
 *
 * @package   Admin
 * @copyright 2004-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminDBOrder extends AdminOrder
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

			$message = new SwatMessage(
				Admin::_('A database error has occured. The item was not saved.'),
				'system-error');

			$this->app->messages->add($message);
			$e->process();

		} catch (SwatException $e) {
			$message = new SwatMessage(
				Admin::_('An error has occured. The item was not saved.'),
				'system-error');

			$this->app->messages->add($message);
			$e->process();
		}
	}

	// }}}
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$this->saveIndexes();
	}

	// }}}
}

?>
