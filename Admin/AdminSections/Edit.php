<?php

require_once("Admin/AdminPage.php");
require_once('Swat/SwatLayout.php');
require_once("MDB2.php");

class AdminSectionsEdit extends AdminPage {

	private $layout;

	public function init($app) {
		$this->layout = new SwatLayout('Admin/AdminSections/edit.xml');
	}

	public function display($app) {
		/*
		$sql = 'select sectionid, title, hidden from adminsections';
		$types = array('integer', 'text', 'boolean');
		$result = $app->db->query($sql, $types);

		if (MDB2::isError($result)) 
			throw new Exception($result->getMessage());
		*/
		/*
		$store = new SwatTableStore();

		while ($row = $result->fetchRow(MDB2_FETCHMODE_OBJECT))
			$store->addRow($row, $row->sectionid);

		$view = $this->layout->getWidget('view');
		$view->model = $store;
		*/
		$root = $this->layout->getRoot();
		$root->displayTidy();
	}

	public function process($app) {
		$root = $this->layout->getRoot();
		$root->process();
	}
}
?>