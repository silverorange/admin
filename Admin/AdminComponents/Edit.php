<?php

require_once("Admin/Admin/Edit.php");
require_once('Admin/AdminUI.php');
require_once('Admin/AdminDB.php');
require_once("MDB2.php");

/**
 * Edit page for AdminComponents
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsEdit extends AdminEdit {

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminComponents/edit.xml');

		$sectionfly = $this->ui->getWidget('section');
		$sectionfly->options = AdminDB::getOptionArray($this->app->db, 
			'adminsections', 'title', 'sectionid', 'displayorder');
	}

	protected function saveData($id) {
		$db = $this->app->db;

		if ($id == 0)
			$sql = 'insert into admincomponents(title, shortname, section,
				hidden, description) 
				values (%s, %s, %s, %s, %s)';
		else
			$sql = 'update admincomponents
				set title = %s,
					shortname = %s,
					section = %s,
					hidden = %s,
					description = %s
				WHERE componentid = %s';

		$sql = sprintf($sql,
			$db->quote($this->ui->getWidget('title')->value, 'text'),
			$db->quote($this->ui->getWidget('shortname')->value, 'text'),
			$db->quote($this->ui->getWidget('section')->value, 'integer'),
			$db->quote($this->ui->getWidget('hidden')->value, 'boolean'),
			$db->quote($this->ui->getWidget('description')->value, 'text'),
			$db->quote($id, 'integer'));

		$db->query($sql);
	}

	protected function loadData($id) {
		$sql = 'SELECT title, shortname, section, hidden, description
			FROM admincomponents WHERE componentid = %s';

		$sql = sprintf($sql,
			$this->app->db->quote($id, 'integer'));

		$rs = $this->app->db->query($sql, array('text', 'text', 'integer', 
			'boolean', 'text'));

		$row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT);

		$this->ui->getWidget('title')->value = $row->title;
		$this->ui->getWidget('shortname')->value = $row->shortname;
		$this->ui->getWidget('section')->value = $row->section;
		$this->ui->getWidget('hidden')->value = $row->hidden;
		$this->ui->getWidget('description')->value = $row->description;
	}
}
?>
