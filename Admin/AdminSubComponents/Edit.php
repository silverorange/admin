<?php

require_once 'Admin/Admin/DBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for AdminSubComponents
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminSubComponentsEdit extends AdminDBEdit {

	private $fields;
	private $parent;

	public function init()
	{
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminSubComponents/edit.xml');

		$this->parent = SwatApplication::initVar('parent');


		$this->fields = array('title', 'shortname', 'boolean:show', 'integer:component');

		$form = $this->ui->getWidget('edit_form');
		$form->addHiddenField('parent', $this->parent);
	}

	public function displayInit()
	{
		parent::displayInit();
		
		//rebuild the navbar
		$parent_title = SwatDB::queryOne($this->app->db, 'admincomponents', 'text:title',
			'componentid', $this->parent);

		$this->navbar->popElements();
		$this->navbar->addElement('Admin Components', 'AdminComponents');
		$this->navbar->addElement($parent_title, 'AdminComponents/Details?id='.$this->parent);
	}
	
	protected function processPage($id)
	{
		$shortname = $this->ui->getWidget('shortname');

		$query = SwatDB::query($this->app->db, sprintf('select shortname from
			adminsubcomponents where shortname = %s and subcomponentid != %s and component != %s',
			$this->app->db->quote($shortname->value, 'text'),
			SwatDB::equalityOperator($id, true),
			$this->app->db->quote($id, 'integer'),
			$this->app->db->quote($this->parent, 'integer')));

		if ($query->numRows()) {
			$msg = new SwatMessage(Admin::_('Shortname already exists and must be unique.'), SwatMessage::USER_ERROR);
			$shortname->addMessage($msg);
		}
	}

	protected function saveDBData($id)
	{
		$values = $this->ui->getValues(array('title', 'shortname', 'show'));
		$values['component'] = $this->parent;

		if ($id == 0)
			$id = SwatDB::insertRow($this->app->db, 'adminsubcomponents', $this->fields,
				$values, 'integer:subcomponentid');
		else
			SwatDB::updateRow($this->app->db, 'adminsubcomponents', $this->fields,
				$values, 'integer:subcomponentid', $id);

		$msg = new SwatMessage(sprintf(Admin::_('Sub-Component "%s" has been saved.'), $values['title']), SwatMessage::INFO);
		$this->app->addMessage($msg);
	}

	protected function loadDBData($id)
	{
		$row = SwatDB::queryRow($this->app->db, 'adminsubcomponents', 
			$this->fields, 'integer:subcomponentid', $id);

		if ($row === null)
			return $this->app->replacePage('Admin/NotFound');

		$this->ui->setValues(get_object_vars($row));

		$this->parent = intval($row->component);
		$form = $this->ui->getWidget('edit_form');
		$form->addHiddenField('parent', $this->parent);
	}
}

?>
