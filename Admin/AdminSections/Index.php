<?php

require_once("Admin/AdminPage.php");
require_once('Admin/AdminTableStore.php');
require_once('Swat/SwatLayout.php');
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
		$store = $this->app->db->query($sql, $types, true, 'AdminTableStore');

		$view = $this->layout->getWidget('view');
		$view->model = $store;

		$root = $this->layout->getRoot();
		$root->display();
	}

	public function process() {
		$form = $this->layout->getWidget('indexform');
		$form->process();
	}
}

?>
