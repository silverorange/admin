<?php

require_once 'Admin/Admin/Order.php';

/**
 * DB admin ordering page
 *
 * An ordering page with DB error checking.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminDBOrder extends AdminOrder
{
	protected function saveData()
	{
		try {
			$this->app->db->beginTransaction();
			parent::saveData();
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
}

?>
