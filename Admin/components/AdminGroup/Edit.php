<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/dataobjects/AdminGroup.php';

/**
 * Edit page for AdminGroups component
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 */
class AdminAdminGroupEdit extends AdminDBEdit
{
	// {{{ private properties

	private $fields;
	private $group;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->initGroup();

		$this->ui->loadFromXML(dirname(__FILE__).'/admin-group-edit.xml');

		$this->fields = array('title');

		$user_list = $this->ui->getWidget('users');
		$user_list_options = SwatDB::getOptionArray($this->app->db,
			'AdminUser', 'name', 'id', 'name');

		$user_list->addOptionsByArray($user_list_options);

		$component_list = $this->ui->getWidget('components');
		$component_list->setTree(SwatDB::getGroupedOptionArray($this->app->db,
			'AdminComponent', 'title', 'id', 'AdminSection', 'title', 'id',
			'section', 'AdminSection.displayorder, AdminSection.title,
			AdminComponent.displayorder,  AdminComponent.title'));
	}

	// }}}
	// {{{ protected function initGroup()
	protected function initGroup()
	{
		$this->group = new AdminGroup();
		$this->group->setDatabase($this->app->db);

		if ($this->id !== null){
			if (!$this->group->load($this->id)) {
				throw new AdminNotFoundException(
					sprintf(Admin::_('Section with id "%s" notfound.'),
							$this->id));
			}
		}
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array('title'));
		
		$this->group->title = $values['title'];
		$this->group->save();

		$user_list = $this->ui->getWidget('users');

		SwatDB::updateBinding($this->app->db, 'AdminUserAdminGroupBinding',
			'groupnum', $this->group->id, 'usernum', $user_list->values,
			'AdminUser', 'id');

		$component_list = $this->ui->getWidget('components');

		SwatDB::updateBinding($this->app->db, 'AdminComponentAdminGroupBinding',
			'groupnum', $this->group->id, 'component', $component_list->values,
			'AdminComponent', 'id');

		$message = new SwatMessage(
			sprintf(Admin::_('Group “%s” has been saved.'), $values['title']),
			SwatMessage::NOTIFICATION);

		$this->app->messages->add($message);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->group));

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
