<?php

require_once 'Admin/Admin/DBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for AdminComponents
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsEdit extends AdminDBEdit
{
	private $fields;

	public function init()
	{
		parent::init();

		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminComponents/edit.xml');

		$section_flydown = $this->ui->getWidget('section');
		$section_flydown->addOptionsByArray(SwatDB::getOptionArray($this->app->db, 
			'adminsections', 'title', 'id', 'displayorder'));

		$group_list = $this->ui->getWidget('groups');
		$group_list->options = SwatDB::getOptionArray($this->app->db, 
			'admingroups', 'title', 'id', 'title');

		$this->fields = array('title', 'shortname', 'integer:section', 
			'boolean:show', 'boolean:enabled', 'description');
	}

	protected function processPage($id)
	{
		$shortname = $this->ui->getWidget('shortname');

		$query = SwatDB::query($this->app->db, sprintf('select shortname from
			admincomponents where shortname = %s and id %s %s',
			$this->app->db->quote($shortname->value, 'text'),
			SwatDB::equalityOperator($id, true),
			$this->app->db->quote($id, 'integer')));

		if ($query->numRows()) {
			$msg = new SwatMessage(Admin::_('Shortname already exists and must be unique.'), SwatMessage::ERROR);
			$shortname->addMessage($msg);
		}
	}

	protected function saveDBData($id)
	{
		$values = $this->ui->getValues(array('title', 'shortname', 'section', 
			'show', 'enabled', 'description'));

		if ($id == 0)
			$id = SwatDB::insertRow($this->app->db, 'admincomponents', $this->fields,
				$values, 'integer:id');
		else
			SwatDB::updateRow($this->app->db, 'admincomponents', $this->fields,
				$values, 'integer:id', $id);

		$group_list = $this->ui->getWidget('groups');

		SwatDB::updateBinding($this->app->db, 'admincomponent_admingroup', 
			'component', $id, 'groupnum', $group_list->values, 'admingroups', 'groupid');
		
		$msg = new SwatMessage(sprintf(Admin::_('Component "%s" has been saved.'), $values['title']), SwatMessage::NOTIFICATION);
		$this->app->messages->add($msg);
	}

	protected function loadDBData($id)
	{
		$row = SwatDB::queryRowFromTable($this->app->db, 'admincomponents', 
			$this->fields, 'integer:id', $id);

		if ($row === null)
			return $this->app->replacePageNoAccess();

		$this->ui->setValues(get_object_vars($row));

		$group_list = $this->ui->getWidget('groups');
		$group_list->values = SwatDB::queryColumn($this->app->db, 
			'admincomponent_admingroup', 'groupnum', 'component', $id);
	}
}

?>
