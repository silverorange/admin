<?php

require_once 'Admin/Admin/DBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for AdminGroups component
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminGroupsEdit extends AdminDBEdit
{
	private $fields;

	public function init()
	{
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminGroups/edit.xml');

		$this->fields = array('title');

		$user_list = $this->ui->getWidget('users');
		$user_list->options = SwatDB::getOptionArray($this->app->db, 
			'adminusers', 'name', 'userid', 'name');

		$component_list = $this->ui->getWidget('components');
		$component_list->tree = SwatDB::getGroupedOptionArray($this->app->db, 
			'admincomponents', 'title', 'componentid',
			'adminsections', 'title', 'sectionid', 'section',
			'adminsections.displayorder, adminsections.title,
			admincomponents.displayorder,  admincomponents.title');
	}

	protected function saveDBData($id)
	{
		$values = $this->ui->getValues(array('title'));

		if ($id == 0)
			$id = SwatDB::insertRow($this->app->db, 'admingroups', $this->fields,
				$values, 'integer:groupid');
		else
			SwatDB::updateRow($this->app->db, 'admingroups', $this->fields,
				$values, 'integer:groupid', $id);

		$user_list = $this->ui->getWidget('users');

		SwatDB::updateBinding($this->app->db, 'adminuser_admingroup', 
			'groupnum', $id, 'usernum', $user_list->values, 'adminusers', 'userid');

		$component_list = $this->ui->getWidget('components');

		SwatDB::updateBinding($this->app->db, 'admincomponent_admingroup', 
			'groupnum', $id, 'component', $component_list->values, 'admincomponents', 'componentid');

		$msg = new SwatMessage(sprintf(Admin::_('Group "%s" has been saved.'), $values['title']), SwatMessage::INFO);
		$this->app->addMessage($msg);
	}

	protected function loadDBData($id)
	{
		$row = SwatDB::queryRow($this->app->db, 'admingroups', 
			$this->fields, 'integer:groupid', $id);

		if ($row === null)
			return $this->app->replacePage('Admin/NotFound');

		$this->ui->setValues(get_object_vars($row));
		
		$user_list = $this->ui->getWidget('users');
		$user_list->values = SwatDB::queryColumn($this->app->db, 
			'adminuser_admingroup', 'usernum', 'groupnum', $id);
		
		$component_list = $this->ui->getWidget('components');
		$component_list->values = SwatDB::queryColumn($this->app->db, 
			'admincomponent_admingroup', 'component', 'groupnum', $id);
	}
}

?>
