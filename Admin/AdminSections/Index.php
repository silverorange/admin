<?php

require_once("Admin/AdminPage.php");
require_once('Swat/SwatLayout.php');
require_once('Swat/SwatTableStore.php');
require_once("MDB2.php");

class AdminSectionsIndex extends AdminPage {

	private $layout;

	public function init() {
		$this->layout = new SwatLayout('Admin/AdminSections/index.xml');
	}

	public function display() {
		$sql = 'SELECT sectionid, title, hidden 
				FROM adminsections 
				ORDER BY displayorder';

		$types = array('integer', 'text', 'boolean');
		$result = $this->app->db->query($sql, $types);

		if (MDB2::isError($result)) 
			throw new Exception($result->getMessage());

		$store = new SwatTableStore();

		while ($row = $result->fetchRow(MDB2_FETCHMODE_OBJECT))
			$store->addRow($row, $row->sectionid);

		$view = $this->layout->getWidget('view');
		$view->model = $store;

		$root = $this->layout->getRoot();
		$root->display();
	}

	public function process() {
		$root = $this->layout->getRoot();
		$root->process();
	}
}

?>
