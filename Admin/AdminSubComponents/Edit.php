<?php

require_once('Admin/Admin/Edit.php');
require_once('Admin/AdminUI.php');
require_once('SwatDB/SwatDB.php');

/**
 * Edit page for AdminSubComponents
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminSubComponentsEdit extends AdminEdit {

	private $fields;
	private $parent;

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminSubComponents/edit.xml');

		$this->fields = array('title', 'shortname', 'boolean:show', 'integer:component');

		$this->parent = SwatApplication::initVar('parent');
		$form = $this->ui->getWidget('editform');
		$form->addHiddenField('parent', $this->parent);
	}

	protected function saveData($id) {

		$values = $this->ui->getValues(array('title', 'shortname', 'show'));
		$values['component'] = $this->parent;

		if ($id == 0)
			$id = SwatDB::insertRow($this->app->db, 'adminsubcomponents', $this->fields,
				$values, 'integer:subcomponentid');
		else
			SwatDB::updateRow($this->app->db, 'adminsubcomponents', $this->fields,
				$values, 'integer:subcomponentid', $id);

		$this->app->addMessage(sprintf(_S('Sub-Component "%s" has been saved.'), $values['title']));
	}

	protected function loadData($id) {

		$row = SwatDB::queryRow($this->app->db, 'adminsubcomponents', 
			$this->fields, 'integer:subcomponentid', $id);

		$this->ui->setValues(get_object_vars($row));

		$this->parent = intval($row->component);
		$form = $this->ui->getWidget('editform');
		$form->addHiddenField('parent', $this->parent);
	}
}
?>
