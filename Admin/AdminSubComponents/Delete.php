<?php

require_once('Admin/Admin/Confirmation.php');
require_once('SwatDB/SwatDB.php');
require_once('Admin/AdminDependency.php');

/**
 * Delete confirmation page for AdminSubComponents
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminSubComponentsDelete extends AdminDBDelete {

	public function displayInit() {
		$item_list = $this->getItemList('integer');
		
		$dep = new AdminDependency();
		$dep->title = 'subcomponent';
		$dep->status_level = AdminDependency::DELETE;

		$dep->entries = AdminDependency::queryDependencyEntries($this->app->db, 'adminsubcomponents',
			'integer:subcomponentid', null, 'text:title', 'displayorder, title', 
			'subcomponentid in ('.$item_list.')');

		$message = $this->ui->getWidget('message');
		$message->content = $dep->getMessage();
		
		if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0)
			$this->displayCancelButton();

		parent::displayInit();
	}

	protected function processDBData() {
		parent::processDBData();
		
		$item_list = $this->getItemList('integer');
		
		$sql = 'delete from adminsubcomponents where subcomponentid in (%s)';

		$sql = sprintf($sql, $item_list);
		$this->app->db->query($sql);

		$msg = new SwatMessage(sprintf(_nS("%d sub-component has been deleted.", 
			"%d sub-components have been deleted.", count($items)), count($items)), SwatMessage::INFO);

		$this->app->addMessage($msg);
	}
}

?>
