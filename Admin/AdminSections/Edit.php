<?php

require_once("Admin/AdminPage.php");
require_once('Admin/AdminUI.php');
require_once("MDB2.php");

class AdminSectionsEdit extends AdminPage {

	private $ui;

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminSections/edit.xml');
	}

	public function display() {
		$id = intval(SwatApplication::initVar('id'));
		$btn_submit = $this->ui->getWidget('btn_submit');

		if ($id == 0) {
			$btn_submit->setTitleFromStock('create');
		} else {
			$this->loadData($id);
			$btn_submit->setTitleFromStock('apply');
		}

		$form = $this->ui->getWidget('editform');
		$form->action = $this->source;
		$form->addHiddenField('id', $id);

		$root = $this->ui->getRoot();
		$root->display();
	}

	public function process() {
		$form = $this->ui->getWidget('editform');
		$id = intval(SwatApplication::initVar('id'));

		if ($form->process()) {
			if (!$form->hasErrorMessage()) {
				$this->saveData($id);
				$this->app->relocate($this->component);
			}
		}
	}

	private function saveDate($id) {
		$db = $this->app->db;

		if ($id == 0)
			$sql = 'INSERT INTO adminsections(title, hidden, description)
				VALUES (%s, %s, %s)';
		else
			$sql = 'UPDATE adminsections
				SET title = %s,
					hidden = %s,
					description = %s
				WHERE sectionid = %s';

		$sql = sprintf($sql,
			$db->quote($this->ui->getWidget('title')->value, 'text'),
			$db->quote($this->ui->getWidget('hidden')->value, 'boolean'),
			$db->quote($this->ui->getWidget('description')->value, 'text'),
			$db->quote($id, 'integer'));

		$db->query($sql);
	}

	private function loadData($id) {
		$sql = 'SELECT title, hidden, description
			FROM adminsections WHERE sectionid = %s';

		$sql = sprintf($sql,
			$this->app->db->quote($id, 'integer'));

		$rs = $this->app->db->query($sql, array('text', 'boolean', 'text'));
		$row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT);

		$this->ui->getWidget('title')->value = $row->title;
		$this->ui->getWidget('hidden')->value = $row->hidden;
		$this->ui->getWidget('description')->value = $row->description;
	}
}
?>
