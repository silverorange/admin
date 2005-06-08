<?php

require_once 'Admin/Admin/DBDelete.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminDependency.php';

/**
 * Delete confirmation page for AdminComponents
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminSectionsDelete extends AdminDBDelete {

	public function displayInit() {
		$item_list = $this->getItemList('integer');
		
		$dep = new AdminDependency();
		$dep->title = 'Admin Section';
		$dep->status_level = AdminDependency::DELETE;

		$dep->entries = AdminDependency::queryDependencyEntries($this->app->db, 'adminsections',
			'integer:sectionid', null, 'text:title', 'title', 'sectionid in ('.$item_list.')');

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		
		parent::displayInit();
	}

	protected function processDBData() {
		parent::processDBData();

		$sql = 'delete from adminsections where sectionid in (%s)';
		$item_list = $this->getItemList('integer');
		$sql = sprintf($sql, $item_list);
		SwatDB::query($this->app->db, $sql);

		$msg = new SwatMessage(sprintf(_nS("%d admin section has been deleted.", 
			"%d admin sections have been deleted.", $this->getItemCount()), $this->getItemCount()),
			SwatMessage::INFO);

		$this->app->addMessage($msg);	
	}
}

?>
