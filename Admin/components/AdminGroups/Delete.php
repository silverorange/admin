<?php

require_once 'Admin/pages/AdminDBDelete.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminDependency.php';

/**
 * Delete confirmation page for AdminGroups component
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminGroupsDelete extends AdminDBDelete
{
	// process phase
	// {{{ protected function processDBData()

	protected function processDBData()
	{
		parent::processDBData();

		$sql = 'delete from admingroups where id in (%s)';
		$item_list = $this->getItemList('integer');
		$sql = sprintf($sql, $item_list);
		SwatDB::query($this->app->db, $sql);

		$msg = new SwatMessage(sprintf(Admin::ngettext("%d admin group has been deleted.", 
			"%d admin groups have been deleted.", $this->getItemCount()), $this->getItemCount()),
			SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);
	}

	// }}}

	// build phase
	// {{{ protected function initDisplay()

	protected function initDisplay()
	{
		parent::initDisplay();

		$item_list = $this->getItemList('integer');
		
		$dep = new AdminDependency();
		$dep->title = 'Admin Group';
		$dep->status_level = AdminDependency::DELETE;

		$dep->entries = AdminDependency::queryDependencyEntries($this->app->db, 'admingroups',
			'integer:id', null, 'text:title', 'title', 'id in ('.$item_list.')');

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
	}

	// }}}
}

?>
