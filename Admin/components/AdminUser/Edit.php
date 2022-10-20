<?php

/**
 * Edit page for AdminUsers component
 *
 * @package   Admin
 * @copyright 2005-2022 silverorange
 */
class AdminAdminUserEdit extends AdminObjectEdit
{
	// {{{ protected function getObjectClass()

	protected function getObjectClass()
	{
		return 'AdminUser';
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return __DIR__.'/edit.xml';
	}

	// }}}
	// {{{ protected function getObjectUiValueNames()

	protected function getObjectUiValueNames()
	{
		return array(
			'email',
			'name',
			'enabled',
			'force_change_password',
			'two_fa_enabled',
		);
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->initPasswordWidgets();
		$this->initGroups();
		$this->initInstances();

		$this->ui->getWidget('two_fa_enabled')->parent->visible =
			$this->app->is2FaEnabled();
	}

	// }}}
	// {{{ protected function initPasswordWidgets()

	protected function initPasswordWidgets()
	{
		$confirm_widget = $this->ui->getWidget('confirm_password');
		$confirm_widget->password_widget = $this->ui->getWidget('password');

		if ($this->isNew()) {
			$confirm_widget->required = true;
			$this->ui->getWidget('password')->required = true;
			$this->ui->getWidget('confirm_password_field')->note = null;
			$this->ui->getWidget('password_disclosure')->open = true;
			$this->ui->getWidget('password_disclosure')->title =
				Admin::_('Set Password');
		}
	}

	// }}}
	// {{{ protected function initGroups()

	protected function initGroups()
	{
		$group_list = $this->ui->getWidget('groups');
		$group_list_options = SwatDB::getOptionArray(
			$this->app->db,
			'AdminGroup',
			'title',
			'id',
			'title'
		);

		$group_list->addOptionsByArray($group_list_options);
	}

	// }}}
	// {{{ protected function initInstances()

	protected function initInstances()
	{
		if ($this->app->isMultipleInstanceAdmin()) {
			$this->ui->getWidget('instances')->parent->visible = true;

			$instance_list = $this->ui->getWidget('instances');
			$instance_list_options = SwatDB::getOptionArray(
				$this->app->db,
				'Instance',
				'title',
				'id',
				'shortname'
			);

			$instance_list->addOptionsByArray($instance_list_options);
		}
	}

	// }}}

	// process phase
	// {{{ protected function validate()

	protected function validate()
	{
		$email = $this->ui->getWidget('email');

		$class_name = SwatDBClassMap::get('AdminUser');
		$user = new $class_name();
		$user->setDatabase($this->app->db);

		if ($user->loadFromEmail($email->value)) {
			if ($user->id !== $this->getObject()->id) {
				$message = new SwatMessage(
					Admin::_(
						'An account with this email address already exists.'
					)
				);

				$email->addMessage($message);
			}
		}

		if ($this->ui->getWidget('confirm_password_field')->hasMessage() ||
			$this->ui->getWidget('password')->hasMessage()) {
			$this->ui->getWidget('password_disclosure')->open = true;
		}
	}

	// }}}
	// {{{ protected function updateObject()

	protected function updateObject()
	{
		parent::updateObject();

		$this->updatePassword();
	}

	// }}}
	// {{{ protected function updatePassword()

	protected function updatePassword()
	{
		$password = $this->ui->getWidget('password')->value;

		if ($password != '') {
			$crypt = $this->app->getModule('SiteCryptModule');
			$this->getObject()->setPasswordHash(
				$crypt->generateHash($password)
			);
		}
	}

	// }}}
	// {{{ protected function postSaveObject()

	protected function postSaveObject()
	{
		$this->updateGroupBindings();
		$this->updateInstanceBindings();
	}

	// }}}
	// {{{ protected function updateGroupBindings()

	protected function updateGroupBindings()
	{
		$group_list = $this->ui->getWidget('groups');

		SwatDB::updateBinding(
			$this->app->db,
			'AdminUserAdminGroupBinding',
			'usernum',
			$this->getObject()->id,
			'groupnum',
			$group_list->values,
			'AdminGroup',
			'id'
		);
	}

	// }}}
	// {{{ protected function updateInstanceBindings()

	protected function updateInstanceBindings()
	{
		if ($this->app->isMultipleInstanceAdmin()) {
			$instance_list = $this->ui->getWidget('instances');
			SwatDB::updateBinding(
				$this->app->db,
				'AdminUserInstanceBinding',
				'usernum',
				$this->getObject()->id,
				'instance',
				$instance_list->values,
				'Instance',
				'id'
			);
		}
	}

	// }}}
	// {{{ protected function getSavedMessagePrimaryContent()

	protected function getSavedMessagePrimaryContent()
	{
		return sprintf(
			Admin::_('User “%s” has been saved.'),
			$this->getObject()->email
		);
	}

	// }}}

	// build phase
	// {{{ protected function loadObject()

	protected function loadObject()
	{
		parent::loadObject();

		if (!$this->isNew()) {
			$this->loadGroupBindings();
			$this->loadInstanceBindings();
		}
	}

	// }}}
	// {{{ protected function loadGroupBindings()

	protected function loadGroupBindings()
	{
		$group_list = $this->ui->getWidget('groups');
		$group_list->values = SwatDB::queryColumn(
			$this->app->db,
			'AdminUserAdminGroupBinding',
			'groupnum',
			'usernum',
			$this->getObject()->id
		);
	}

	// }}}
	// {{{ protected function loadInstanceBindings()

	protected function loadInstanceBindings()
	{
		if ($this->app->isMultipleInstanceAdmin()) {
			$instance_list = $this->ui->getWidget('instances');
			$instance_list->values = SwatDB::queryColumn(
				$this->app->db,
				'AdminUserInstanceBinding',
				'instance',
				'usernum',
				$this->getObject()->id
			);
		}
	}

	// }}}
}

?>
