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

	private $fields;

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminComponents/edit.xml');

		$sectionfly = $this->ui->getWidget('section');
		$sectionfly->options = AdminDB::getOptionArray($this->app->db, 
			'adminsections', 'title', 'sectionid', 'displayorder');

		$grouplist = $this->ui->getWidget('groups');
		$grouplist->options = AdminDB::getOptionArray($this->app->db, 
			'admingroups', 'title', 'groupid', 'title');

		$this->fields = array('title', 'shortname', 'integer:section', 
			'boolean:hidden', 'description');
	}

	protected function saveData($id) {

		$values = $this->ui->getValues(array('title', 'shortname', 'section', 
			'hidden', 'description'));

		$this->app->db->beginTransaction();

		if ($id == 0)
			$id = AdminDB::rowInsert($this->app->db, 'admincomponents', $this->fields,
				$values, 'integer:componentid');
		else
			AdminDB::rowUpdate($this->app->db, 'admincomponents', $this->fields,
				$values, 'integer:componentid', $id);

		$grouplist = $this->ui->getWidget('groups');

		AdminDB::bindingUpdate($this->app->db, 'admincomponent_admingroup', 
			'component', $id, 'groupnum', $grouplist->values, 'admingroups', 'groupid');
		
		//bindingUpdate($db, $table, $id_field, $id, $value_field, $values, $other_table, $other_field);

		$this->app->db->commit();
	}

	protected function loadData($id) {

		$row = AdminDB::rowQuery($this->app->db, 'admincomponents', 
			$this->fields, 'integer:componentid', $id);

		$this->ui->setValues(get_object_vars($row));

		$grouplist = $this->ui->getWidget('groups');
		$grouplist->values = AdminDB::bindingQuery($this->app->db, 
			'admincomponent_admingroup', 'component', $id, 'groupnum');
	}
}
?>
