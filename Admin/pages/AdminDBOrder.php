<?php

require_once 'Admin/pages/AdminOrder.php';
require_once 'SwatDB/SwatDBTransaction.php';

/**
 * DB admin ordering page
 *
 * An ordering page with DB error checking.
 *
 * @package   Admin
 * @copyright 2004-2006 silverorange
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

			$msg = new SwatMessage(
				Admin::_('A database error has occured. The item was not saved.'),
				 SwatMessage::SYSTEM_ERROR);

			$this->app->messages->add($msg);
			$e->process();

		} catch (SwatException $e) {
			$msg = new SwatMessage(
				Admin::_('An error has occured. The item was not saved.'),
				SwatMessage::SYSTEM_ERROR);

			$this->app->messages->add($msg);
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
