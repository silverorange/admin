<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/dataobjects/AdminUser.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Swat/SwatString.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for AdminUsers component
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 */
class AdminAdminUserEdit extends AdminDBEdit
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

		$this->fields = array('email', 'name', 'boolean:enabled',
			'boolean:force_change_password');
		
		$group_list = $this->ui->getWidget('groups');
		$group_list->options = SwatDB::getOptionArray($this->app->db,
			'AdminGroup', 'title', 'id', 'title');
		
		$confirm = $this->ui->getWidget('confirm_password');
		$confirm->password_widget = $this->ui->getWidget('password');;
		
		if ($this->id === null) {
			$this->ui->getWidget('password')->required = true;
			$confirm->required = true;
			
			$this->ui->getWidget('confirm_password_field')->note = null;
			$this->ui->getWidget('password_disclosure')->open = true;
			$this->ui->getWidget('password_disclosure')->title =
				Admin::_('Set Password');
		}
	}

	// }}}

	// process phase
	// {{{ protected function validate()

	protected function validate()
	{
		$email = $this->ui->getWidget('email');

		$query = SwatDB::query($this->app->db, sprintf('select email
			from AdminUser where email = %s and id %s %s',
			$this->app->db->quote($email->value, 'text'),
			SwatDB::equalityOperator($this->id, true),
			$this->app->db->quote($this->id, 'integer')));

		if (count($query) > 0) {
			$message = new SwatMessage(
				Admin::_('An account with this email address already exists.'),
				SwatMessage::ERROR);

			$email->addMessage($message);
		}

		if ($this->ui->getWidget('confirm_password_field')->hasMessage() ||
			$this->ui->getWidget('password')->hasMessage())
			$this->ui->getWidget('password_disclosure')->open = true;
	}

	// }}}
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array('email', 'name', 'enabled',
			'force_change_password'));

		$password = $this->ui->getWidget('password');
		if ($password->value !== null) {
			$salt = SwatString::getSalt(AdminUser::PASSWORD_SALT_LENGTH);
			$values['password_salt'] = $salt;
			$values['password'] = md5($password->value.$salt);
			$this->fields[] = 'password_salt';
			$this->fields[] = 'password';
		}

		if ($this->id === null)
			$this->id = SwatDB::insertRow($this->app->db, 'AdminUser',
				$this->fields, $values, 'integer:id');
		else
			SwatDB::updateRow($this->app->db, 'AdminUser', $this->fields,
				$values, 'integer:id', $this->id);

		$group_list = $this->ui->getWidget('groups');

		SwatDB::updateBinding($this->app->db, 'AdminUserAdminGroupBinding',
			'usernum', $this->id, 'groupnum', $group_list->values, 'AdminGroup',
			'id');
		
		$message = new SwatMessage(
			sprintf(Admin::_('User “%s” has been saved.'), $values['email']),
			SwatMessage::NOTIFICATION);

		$this->app->messages->add($message);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$row = SwatDB::queryRowFromTable($this->app->db, 'AdminUser',
			$this->fields, 'integer:id', $this->id);

		if ($row === null)
			throw new AdminNotFoundException(
				sprintf(Admin::_('User with id ‘%s’ not found.'), $this->id));

		$this->ui->setValues(get_object_vars($row));
		
		$group_list = $this->ui->getWidget('groups');
		$group_list->values = SwatDB::queryColumn($this->app->db,
			'AdminUserAdminGroupBinding', 'groupnum', 'usernum', $this->id);
	}

	// }}}
}

?>
