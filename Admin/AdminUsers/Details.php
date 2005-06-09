<?php

require_once 'Admin/AdminUI.php';
require_once 'Admin/Admin/Index.php';
require_once 'Admin/AdminTableStore.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Details page for AdminUsers component
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminUsersDetails extends AdminIndex {

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminUsers/details.xml');
	}

	public function displayInit() {
		$id = $this->app->initVar('id');

		$row = SwatDB::queryRow($this->app->db, 'adminusers',
			array('username','name'), 'userid' , $id);

		$frame = $this->ui->getWidget('index_frame');
		$frame->title.=' - '.$row->name;
		
		parent::displayInit();
	}
	
	
	protected function getTableStore() {
		$id = $this->app->initVar('id');
	
		$sql = 'select logindate, loginagent, remoteip
				from adminuserhistory
				where usernum = %s
				order by %s';

        $sql = sprintf($sql,
			$this->app->db->quote($id, 'integer'),
            $this->getOrderByClause('logindate desc'));

		$store = $this->app->db->query($sql, null, true, 'AdminTableStore');

		return $store;
	}	
}
?>
