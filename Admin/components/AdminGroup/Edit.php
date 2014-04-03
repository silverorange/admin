<?php

require_once 'Admin/pages/AdminObjectEdit.php';
require_once 'Admin/dataobjects/AdminGroup.php';

/**
 * Edit page for AdminGroups component
 *
 * @package   Admin
 * @copyright 2005-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminGroupEdit extends AdminObjectEdit
{
	// {{{ protected function getObjectClass()

	protected function getObjectClass()
	{
		return 'AdminGroup';
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Admin/components/AdminGroup/edit.xml';
	}

	// }}}
	// {{{ protected function getObjectUiValueNames()

	protected function getObjectUiValueNames()
	{
		return array(
			'title',
		);
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->initUsers();
		$this->initComponents();
	}

	// }}}
	// {{{ protected function initUsers()

	protected function initUsers()
	{
		$user_list = $this->ui->getWidget('users');
		$user_list_options = SwatDB::getOptionArray(
			$this->app->db,
			'AdminUser',
			'name',
			'id',
			'name'
		);

		$user_list->addOptionsByArray($user_list_options);
	}

	// }}}
	// {{{ protected function initUsers()

	protected function initComponents()
	{
		$component_list = $this->ui->getWidget('components');
		$component_list_options = SwatDB::getGroupedOptionArray(
			$this->app->db,
			'AdminComponent',
			'title',
			'id',
			'AdminSection',
			'title',
			'id',
			'section',
			'AdminSection.displayorder, AdminSection.title, '.
				'AdminComponent.displayorder,  AdminComponent.title'
		);

		$component_list->setTree($component_list_options);
	}

	// }}}

	// process phase
	// {{{ protected function postSaveObject()

	protected function postSaveObject()
	{
		$this->updateUserBindings();
		$this->updateComponentBindings();
	}

	// }}}
	// {{{ protected function updateUserBindings()

	protected function updateUserBindings()
	{
		$user_list = $this->ui->getWidget('users');

		SwatDB::updateBinding(
			$this->app->db,
			'AdminUserAdminGroupBinding',
			'groupnum',
			$this->getObject()->id,
			'usernum',
			$user_list->values,
			'AdminUser',
			'id'
		);
	}

	// }}}
	// {{{ protected function updateComponentBindings()

	protected function updateComponentBindings()
	{
		$component_list = $this->ui->getWidget('components');

		SwatDB::updateBinding(
			$this->app->db,
			'AdminComponentAdminGroupBinding',
			'groupnum',
			$this->getObject()->id,
			'component',
			$component_list->values,
			'AdminComponent',
			'id'
		);
	}

	// }}}
	// {{{ protected function getSavedMessagePrimaryContent()

	protected function getSavedMessagePrimaryContent()
	{
		return sprintf(
			Admin::_('Group “%s” has been saved.'),
			$this->getObject()->title
		);
	}

	// }}}

	// build phase
	// {{{ protected function loadObject()

	protected function loadObject()
	{
		parent::loadObject();

		if (!$this->isNew()) {
			$this->loadUserBindings();
			$this->loadComponentBindings();
		}
	}

	// }}}
	// {{{ protected function loadUserBindings()

	protected function loadUserBindings()
	{
		$user_list = $this->ui->getWidget('users');
		$user_list->values = SwatDB::queryColumn(
			$this->app->db,
			'AdminUserAdminGroupBinding',
			'usernum',
			'groupnum',
			$this->getObject()->id
		);
	}

	// }}}
	// {{{ protected function loadComponentBindings()

	protected function loadComponentBindings()
	{
		$component_list = $this->ui->getWidget('components');
		$component_list->values = SwatDB::queryColumn(
			$this->app->db,
			'AdminComponentAdminGroupBinding',
			'component',
			'groupnum',
			$this->getObject()->id
		);
	}

	// }}}
}

?>
