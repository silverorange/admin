<?php

require_once("Admin/AdminPage.php");
require_once('Swat/SwatLayout.php');
require_once("MDB2.php");

class AdminSectionsEdit extends AdminPage {

	private $layout;

	public function init() {
		$this->layout = new SwatLayout('Admin/AdminSections/edit.xml');
	}

	public function display() {
		$root = $this->layout->getRoot();
		$root->display();
	}

	public function process() {
		$form = $this->layout->getWidget('editform');
		$form->process();
	}
}
?>
