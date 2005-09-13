<?php

require_once 'Admin/Admin/DBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for AdminSubComponents
 *
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminSubComponentsEdit extends AdminDBEdit
{
	private $fields;
	private $parent;

	public function initDisplay()
	{
		parent::initDisplay();
		
		// rebuild the navbar
		$parent_title = SwatDB::queryOneFromTable($this->app->db, 'admincomponents', 'text:title',
			'id', $this->parent);

		$this->navbar->popEntry();
		$this->navbar->createEntry('Admin Components', 'AdminComponents');
		$this->navbar->createEntry($parent_title, 'AdminComponents/Details?id='.$this->parent);

		$id = $this->app->initVar('id');
		if ($id == 0)
			$this->navbar->createEntry('Add Sub-Component');
		else
			$this->navbar->createEntry('Edit Sub-Component');
	}

	protected function initInternal()
	{
		$this->ui->loadFromXML('Admin/AdminSubComponents/edit.xml');

		$this->parent = SwatApplication::initVar('parent');

		$this->fields = array('title', 'shortname', 'boolean:show', 'integer:component');

		$form = $this->ui->getWidget('edit_form');
		$form->addHiddenField('parent', $this->parent);
	}
	
	protected function processPage($id)
	{
		$shortname = $this->ui->getWidget('shortname');

		$query = SwatDB::queryRow($this->app->db, sprintf('select shortname from
			adminsubcomponents where shortname = %s and id %s %s and component = %s',
			$this->app->db->quote($shortname->value, 'text'),
			SwatDB::equalityOperator($id, true),
			$this->app->db->quote($id, 'integer'),
			$this->app->db->quote($this->parent, 'integer')));

		if ($query !== null) {
			$msg = new SwatMessage(Admin::_('Shortname already exists and must be unique.'), SwatMessage::ERROR);
			$shortname->addMessage($msg);
		}
	}

	protected function saveDBData($id)
	{
		$values = $this->ui->getValues(array('title', 'shortname', 'show'));
		$values['component'] = $this->parent;

		if ($id == 0)
			$id = SwatDB::insertRow($this->app->db, 'adminsubcomponents', $this->fields,
				$values, 'integer:id');
		else
			SwatDB::updateRow($this->app->db, 'adminsubcomponents', $this->fields,
				$values, 'integer:id', $id);

		$msg = new SwatMessage(
			sprintf(Admin::_('Sub-Component &#8220;%s&#8221; has been saved.'),
			$values['title']), SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);
	}

	protected function loadDBData($id)
	{
		$row = SwatDB::queryRowFromTable($this->app->db, 'adminsubcomponents', 
			$this->fields, 'integer:id', $id);

		if ($row === null)
			return $this->app->replacePageNoAccess();

		$this->ui->setValues(get_object_vars($row));

		$this->parent = intval($row->component);
		$form = $this->ui->getWidget('edit_form');
		$form->addHiddenField('parent', $this->parent);
	}
}

?>
