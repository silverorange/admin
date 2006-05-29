<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for AdminGroups component
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 */
class AdminGroupEdit extends AdminDBEdit
{
	// {{{ private properties

	private $fields;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML(dirname(__FILE__).'/edit.xml');

		$this->fields = array('title');

		$user_list = $this->ui->getWidget('users');
		$user_list->options = SwatDB::getOptionArray($this->app->db, 
			'AdminUser', 'name', 'id', 'name');

		$component_list = $this->ui->getWidget('components');
		$component_list->setTree(SwatDB::getGroupedOptionArray($this->app->db, 
			'AdminComponent', 'title', 'id', 'AdminSection', 'title', 'id', 
			'section', 'AdminSection.displayorder, AdminSection.title,
			AdminComponent.displayorder,  AdminComponent.title'));
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array('title'));

		if ($this->id === null)
			$this->id = SwatDB::insertRow($this->app->db, 'AdminGroup',
				$this->fields, $values, 'integer:id');
		else
			SwatDB::updateRow($this->app->db, 'AdminGroup', $this->fields,
				$values, 'integer:id', $this->id);

		$user_list = $this->ui->getWidget('users');

		SwatDB::updateBinding($this->app->db, 'AdminUserAdminGroupBinding', 
			'groupnum', $this->id, 'usernum', $user_list->values, 'AdminUser', 
			'id');

		$component_list = $this->ui->getWidget('components');

		SwatDB::updateBinding($this->app->db, 'AdminComponentAdminGroupBinding', 
			'groupnum', $this->id, 'component', $component_list->values,
			'AdminComponent', 'id');

		$msg = new SwatMessage(
			sprintf(Admin::_('Group “%s” has been saved.'),
			$values['title']), SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$row = SwatDB::queryRowFromTable($this->app->db, 'AdminGroup', 
			$this->fields, 'integer:id', $this->id);

		if ($row === null)
			throw new AdminNotFoundException(
				sprintf(Admin::_("Group with id '%s' not found."), $this->id));

		$this->ui->setValues(get_object_vars($row));

		$user_list = $this->ui->getWidget('users');
		$user_list->values = SwatDB::queryColumn($this->app->db, 
			'AdminUserAdminGroupBinding', 'usernum', 'groupnum', $this->id);

		$component_list = $this->ui->getWidget('components');
		$component_list->values = SwatDB::queryColumn($this->app->db, 
			'AdminComponentAdminGroupBinding', 'component', 'groupnum', 
			$this->id);
	}

	// }}}
}

?>
