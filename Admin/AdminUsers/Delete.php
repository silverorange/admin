<?php

require_once 'Admin/Admin/DBDelete.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminDependency.php';

/**
 * Delete confirmation page for AdminUsers component
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminUsersDelete extends AdminDBDelete
{
	public function initDisplay()
	{
		$item_list = $this->getItemList('integer');
		
		$dep = new AdminDependency();
		$dep->title = 'Admin User';
		$dep->status_level = AdminDependency::DELETE;

		$dep->entries = AdminDependency::queryDependencyEntries($this->app->db, 'adminusers',
			'integer:userid', null, 'text:name', 'name', 'userid in ('.$item_list.')');

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		
		parent::initDisplay();
	}

	protected function processDBData()
	{
		parent::processDBData();

		$sql = 'delete from adminusers where userid in (%s)';
		$item_list = $this->getItemList('integer');
		$sql = sprintf($sql, $item_list);
		SwatDB::query($this->app->db, $sql);

		$msg = new SwatMessage(sprintf(Admin::ngettext("%d admin user has been deleted.", 
			"%d admin users have been deleted.", $this->getItemCount()), $this->getItemCount()),
			SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);	
	}
}

?>
