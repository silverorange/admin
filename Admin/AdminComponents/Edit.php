<?php

require_once('Admin/Admin/Edit.php');
require_once('Admin/AdminUI.php');
require_once('SwatDB/SwatDB.php');

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
		$sectionfly->options = SwatDB::getOptionArray($this->app->db, 
			'adminsections', 'title', 'sectionid', 'displayorder');

		$grouplist = $this->ui->getWidget('groups');
		$grouplist->options = SwatDB::getOptionArray($this->app->db, 
			'admingroups', 'title', 'groupid', 'title');

		$this->fields = array('title', 'shortname', 'integer:section', 
			'boolean:hidden', 'description');
	}

	protected function saveData($id) {

		$values = $this->ui->getValues(array('title', 'shortname', 'section', 
			'hidden', 'description'));

		$this->app->db->beginTransaction();

		if ($id == 0)
			$id = SwatDB::insertRow($this->app->db, 'admincomponents', $this->fields,
				$values, 'integer:componentid');
		else
			SwatDB::updateRow($this->app->db, 'admincomponents', $this->fields,
				$values, 'integer:componentid', $id);

		$grouplist = $this->ui->getWidget('groups');

		SwatDB::updateBinding($this->app->db, 'admincomponent_admingroup', 
			'component', $id, 'groupnum', $grouplist->values, 'admingroups', 'groupid');
		
		$this->app->db->commit();
	}

	protected function loadData($id) {

		$row = SwatDB::queryRow($this->app->db, 'admincomponents', 
			$this->fields, 'integer:componentid', $id);

		$this->ui->setValues(get_object_vars($row));

		$grouplist = $this->ui->getWidget('groups');
		$grouplist->values = SwatDB::queryField($this->app->db, 
			'admincomponent_admingroup', 'groupnum', 'component', $id);
	}
}
?>
