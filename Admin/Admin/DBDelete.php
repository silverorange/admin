<?php

require_once('Admin/Admin/Confirmation.php');
require_once('SwatDB/SwatDB.php');
require_once('Admin/AdminDependency.php');

/**
 * Generic admin database delete page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * delete page, inherit directly from AdminConfirmation instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminDBDelete extends AdminConfirmation {

	protected function processResponse() {
		$form = $this->ui->getWidget('confirmation_form');

		if ($form->button->name == 'yes_button') {


			try {
				$this->app->db->beginTransaction();
				$this->deleteDBData();
				$this->app->db->commit();

			} catch (SwatDBException $e) {
				$this->app->db->rollback();

				$msg = new SwatMessage(_S("A database error has occured. The item(s) was not deleted."),
					 SwatMessage::ERROR);

				$this->app->addMessage($msg);

				$e->process();

			} catch (SwatException $e) {
				$msg = new SwatMessage(_S("An error has occured. The item(s) was not deleted."), SwatMessage::ERROR);
				$this->app->addMessage($msg);

				$e->process();
			}
		}
	}
			
	/**
	 * Delete data from the database
	 *
	 * This method is called to delete data when processing a delete page.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to delete the data.
	 */
	abstract protected function deleteDBData();

}

?>
