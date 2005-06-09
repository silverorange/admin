<?php

require_once('Admin/AdminUI.php');
require_once('Admin/Admin/Index.php');
require_once('Admin/AdminTableStore.php');

/**
 * Login history page for AdminUsers component
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminUsersLoginHistory extends AdminIndex {

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminUsers/loginhistory.xml');
	}
	
	public function process() {
		parent::process();
		
		$pager = $this->ui->getWidget('pager');
		$pager->total_records =	$this->app->db->queryOne('select count(historyid) from adminuserhistory');
		$pager->process();
	}

	protected function getTableStore() {
	
		$pager = $this->ui->getWidget('pager');
		$this->app->db->setLimit($pager->page_size, $pager->current_record);
		
		$sql = 'select usernum, logindate, loginagent, remoteip, username, name
				from adminuserhistory
				inner join adminusers on adminusers.userid = adminuserhistory.usernum
				order by %s';

        $sql = sprintf($sql,
            $this->getOrderByClause('logindate desc'));

		$store = $this->app->db->query($sql, null, true, 'AdminTableStore');

		return $store;
	}	
}
?>
