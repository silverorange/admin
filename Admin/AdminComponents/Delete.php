<?php

require_once 'Admin/Admin/DBDelete.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminDependency.php';

/**
 * Delete confirmation page for AdminComponents
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsDelete extends AdminDBDelete {

	public function displayInit() {
		$item_list = $this->getItemList('integer');
		
		$dep = new AdminDependency();
		$dep->title = 'component';
		$dep->status_level = AdminDependency::DELETE;

		$dep->entries = AdminDependency::queryDependencyEntries($this->app->db, 'admincomponents',
			'integer:componentid', null, 'text:title', 'displayorder, title', 'componentid in ('.$item_list.')');

		$dep_subcomponents = new AdminDependency();
		$dep_subcomponents->title = 'sub-component';
		$dep_subcomponents->status_level = AdminDependency::DELETE;
		$dep_subcomponents->display_count = true;

		$dep_subcomponents->entries = AdminDependency::queryDependencyEntries($this->app->db, 'adminsubcomponents',
			'integer:subcomponentid', 'integer:component', 'text:title', 'title', 'component in ('.$item_list.')');

		$dep->addDependency($dep_subcomponents);

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		
		if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0)
			$this->displayCancelButton();

		parent::displayInit();
	}

	protected function processDBData() {
		parent::processDBData();

		$sql = 'delete from admincomponents where componentid in (%s)';
		$item_list = $this->getItemList('integer');
		$sql = sprintf($sql, $item_list);
		SwatDB::query($this->app->db, $sql);

		$msg = new SwatMessage(sprintf(Admin::ngettext("%d component has been deleted.", 
			"%d components have been deleted.", $this->getItemCount()), $this->getItemCount()),
			SwatMessage::INFO);

		$this->app->addMessage($msg);	
	}
}

?>
