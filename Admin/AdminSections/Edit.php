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
		$id = intval(SwatApplication::initVar('id'));
		$btn_submit = $this->layout->getWidget('btn_submit');

		if ($id == 0) {
			$btn_submit->setTitleFromStock('create');
		} else {
			$this->loadFromDB($id);
			$btn_submit->setTitleFromStock('apply');
		}

		$form = $this->layout->getWidget('editform');
		$form->addHiddenField('id', $id);

		$root = $this->layout->getRoot();
		$root->display();
	}

	public function process() {
		$form = $this->layout->getWidget('editform');
		$form->process();
	}

	private function loadFromDB($id) {
		$sql = 'SELECT title, hidden, description
			FROM adminsections WHERE sectionid = %s';

		$sql = sprintf($sql,
			$this->app->db->quote($id, 'integer'));

		$rs = $this->app->db->query($sql, array('text', 'boolean', 'text'));
		$row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT);

		$this->layout->getWidget('title')->value = $row->title;
		$this->layout->getWidget('hidden')->value = $row->hidden;
		$this->layout->getWidget('description')->value = $row->description;
	}
}
?>
