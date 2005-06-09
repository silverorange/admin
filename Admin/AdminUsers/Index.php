<?php

require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/Admin/Index.php';
require_once 'Admin/AdminTableStore.php';
require_once 'Admin/AdminUsers/HistoryCellRenderer.php';

/**
 * Index page for AdminUsers component
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminUsersIndex extends AdminIndex {

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminUsers/index.xml');
	}

	protected function getTableStore() {
		$view = $this->ui->getWidget('index_view');

		$sql = 'select adminusers.userid, adminusers.username, adminusers.name,
					adminusers.enabled, view_adminuser_lastlogin.lastlogin
				from adminusers 
				left outer join view_adminuser_lastlogin on
					view_adminuser_lastlogin.usernum = adminusers.userid
				order by %s';

		$sql = sprintf($sql, $this->getOrderByClause('adminusers.username'));

		$store = $this->app->db->query($sql, null, true, 'AdminTableStore');

		return $store;
	}

	public function processActions() {
		$view = $this->ui->getWidget('index_view');
		$actions = $this->ui->getWidget('index_actions');

		$num = count($view->checked_items);
		$msg = null;
		
		switch ($actions->selected->id) {
			case 'delete':
				$this->app->replacePage('AdminUsers/Delete');
				$this->app->page->setItems($view->checked_items);
				break;

			case 'enable':
				SwatDB::updateColumn($this->app->db, 'adminusers', 
					'boolean:enabled', true, 'userid', 
					$view->checked_items);

				$msg = new SwatMessage(sprintf(_nS("%d user has been enabled.", 
					"%d users have been enabled.", $num), $num));

				break;

			case 'disable':
				SwatDB::updateColumn($this->app->db, 'adminusers', 
					'boolean:enabled', false, 'userid', 
					$view->checked_items);

				$msg = new SwatMessage(sprintf(_nS("%d user has been disabled.", 
					"%d users have been disabled.", $num), $num));

				break;
		}
		
		if ($msg !== null)
			$this->app->addMessage($msg);
	}
}

?>
