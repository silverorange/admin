<?php

require_once 'Admin/AdminUI.php';
require_once 'Admin/Admin/Index.php';
require_once 'Admin/AdminTableStore.php';

/**
 * Index page for AdminSections
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminGroupsIndex extends AdminIndex {

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminGroups/index.xml');
	}

	protected function getTableStore() {
		$view = $this->ui->getWidget('index_view');

		$sql = 'select groupid, title 
				from admingroups 
				order by title';

		$store = $this->app->db->query($sql, null, true, 'AdminTableStore');

		return $store;
	}

	public function processActions() {
		$view = $this->ui->getWidget('index_view');
		$actions = $this->ui->getWidget('index_actions');

		switch ($actions->selected->id) {
			case 'delete':
				$this->app->replacePage('AdminGroups/Delete');
				$this->app->page->setItems($view->checked_items);
				break;
		}
	}
}

?>
