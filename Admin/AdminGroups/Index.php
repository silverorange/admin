<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/Admin/Index.php';
require_once 'Admin/AdminTableStore.php';

/**
 * Index page for AdminGroups component
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminGroupsIndex extends AdminIndex
{
	public function processActions()
	{
		$view = $this->ui->getWidget('index_view');
		$actions = $this->ui->getWidget('index_actions');

		switch ($actions->selected->id) {
			case 'delete':
				$this->app->replacePage('AdminGroups/Delete');
				$this->app->getPage()->setItems($view->checked_items);
				break;
		}
	}

	protected function initInternal()
	{
		$this->ui->loadFromXML('Admin/AdminGroups/index.xml');
	}

	protected function getTableStore($view)
	{
		$view = $this->ui->getWidget('index_view');

		$sql = 'select id, title 
				from admingroups 
				order by title';

		$store = SwatDB::query($this->app->db, $sql, 'AdminTableStore');

		return $store;
	}
}

?>
