<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/dataobjects/AdminUser.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Swat/SwatString.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/dataobjects/AdminUser.php';

/**
 * Edit page for AdminUsers component
 *
 * @package   Admin
 * @copyright 2005-2012 silverorange
 */
class AdminAdminUserEdit extends AdminDBEdit
{
	// {{{ protected properties

	protected $user;
	protected $ui_xml = 'Admin/components/AdminUser/edit.xml';

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->initUser();

		$this->ui->loadFromXML($this->ui_xml);

		$group_list = $this->ui->getWidget('groups');
		$group_list_options = SwatDB::getOptionArray($this->app->db,
			'AdminGroup', 'title', 'id', 'title');

		$group_list->addOptionsByArray($group_list_options);

		$confirm = $this->ui->getWidget('confirm_password');
		$confirm->password_widget = $this->ui->getWidget('password');

		if ($this->id === null) {
			$confirm->required = true;
			$this->ui->getWidget('password')->required = true;
			$this->ui->getWidget('confirm_password_field')->note = null;
			$this->ui->getWidget('password_disclosure')->open = true;
			$this->ui->getWidget('password_disclosure')->title =
				Admin::_('Set Password');
		}

		if ($this->app->getInstance() !== null) {
			$this->ui->getWidget('instances')->parent->visible = true;

			$instance_list = $this->ui->getWidget('instances');
			$instance_list_options = SwatDB::getOptionArray($this->app->db,
				'Instance', 'shortname', 'id', 'shortname');

			$instance_list->addOptionsByArray($instance_list_options);
		}
	}

	// }}}
	// {{{ protected function initUser()

	protected function initUser()
	{
		$class_name = SwatDBClassMap::get('AdminUser');
		$this->user = new $class_name();
		$this->user->setDatabase($this->app->db);

		if ($this->id !== null) {
			if (!$this->user->load($this->id)) {
				throw new AdminNotFoundException(
					sprintf(Admin::_('User with id "%s" notfound.'),
						$this->id));
			}
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
			if ($user->id !== $this->user->id) {
				$message = new SwatMessage(
					Admin::_('An account with this email address already'.
					' exists.'));

				$email->addMessage($message);
			}
		}

		if ($this->ui->getWidget('confirm_password_field')->hasMessage() ||
			$this->ui->getWidget('password')->hasMessage())
			$this->ui->getWidget('password_disclosure')->open = true;
	}

	// }}}
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$this->updateUser();
		$this->user->save();

		$this->saveBindingTables();

		$message = new SwatMessage(
			sprintf(Admin::_('User “%s” has been saved.'), $values['email']),
			'notice');

		$this->app->messages->add($message);
	}

	// }}}
	// {{{ protected function updateUser()

	protected function updateUser()
	{
		$values = $this->ui->getValues(
			array(
				'email',
				'name',
				'enabled',
				'force_change_password'
			)
		);

		$password = $this->ui->getWidget('password');
		if ($password->value !== null) {
			$this->user->setPassword($password->value);
		}

		$this->user->email = $values['email'];
		$this->user->name = $values['name'];
		$this->user->enabled = $values['enabled'];
		$this->user->force_change_password = $values['force_change_password'];

		return $this->user;
	}

	// }}}
	// {{{ protected function saveBindingTables()

	protected function saveBindingTables()
	{
		$group_list = $this->ui->getWidget('groups');
		SwatDB::updateBinding($this->app->db, 'AdminUserAdminGroupBinding',
			'usernum', $this->user->id, 'groupnum', $group_list->values,
			'AdminGroup', 'id');

		if ($this->app->getInstance() !== null) {
			$instance_list = $this->ui->getWidget('instances');
			SwatDB::updateBinding($this->app->db, 'AdminUserInstanceBinding',
				'usernum', $this->user->id, 'instance', $instance_list->values,
				'Instance', 'id');
		}
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->user));

		// don't set the the password field to the hashed password
		$this->ui->getWidget('password')->value = null;

		$group_list = $this->ui->getWidget('groups');
		$group_list->values = SwatDB::queryColumn($this->app->db,
			'AdminUserAdminGroupBinding', 'groupnum', 'usernum', $this->id);

		if ($this->app->getInstance() !== null) {
			$instance_list = $this->ui->getWidget('instances');
			$instance_list->values = SwatDB::queryColumn($this->app->db,
				'AdminUserInstanceBinding', 'instance', 'usernum', $this->id);
		}
	}

	// }}}
}

?>
