<?php

require_once("Admin/AdminPage.php");
require_once('Admin/AdminUI.php');
require_once("MDB2.php");

/**
 * Edit page for AdminSections
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminSectionsEdit extends AdminPage {

	private $ui;

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminSections/edit.xml');
	}

	public function display() {
		$id = intval(SwatApplication::initVar('id'));
		$btn_submit = $this->ui->getWidget('btn_submit');
		$frame = $this->ui->getWidget('frame');

		if ($id == 0) {
			$btn_submit->setTitleFromStock('create');
			$frame->title = 'New Section';
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
				$this->app->relocate($this->app->getHistory());
			}
		}
	}

	private function saveData($id) {
		$db = $this->app->db;

		if ($id == 0)
			$sql = 'INSERT INTO adminsections(title, show, description)
				VALUES (%s, %s, %s)';
		else
			$sql = 'UPDATE adminsections
				SET title = %s,
					show = %s,
					description = %s
				WHERE sectionid = %s';

		$sql = sprintf($sql,
			$db->quote($this->ui->getWidget('title')->value, 'text'),
			$db->quote($this->ui->getWidget('show')->value, 'boolean'),
			$db->quote($this->ui->getWidget('description')->value, 'text'),
			$db->quote($id, 'integer'));

		$db->query($sql);
	}

	private function loadData($id) {
		$sql = 'SELECT title, show, description
			FROM adminsections WHERE sectionid = %s';

		$sql = sprintf($sql,
			$this->app->db->quote($id, 'integer'));

		$rs = $this->app->db->query($sql, array('text', 'boolean', 'text'));
		$row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT);

		$this->ui->getWidget('title')->value = $row->title;
		$this->ui->getWidget('show')->value = $row->show;
		$this->ui->getWidget('description')->value = $row->description;
	}
}
?>
