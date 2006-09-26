<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
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

		$this->fields = array('username', 'name', 'boolean:enabled');
		
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
		$username = $this->ui->getWidget('username');

		$query = SwatDB::query($this->app->db, sprintf('select username 
			from AdminUser where username = %s and id %s %s',
			$this->app->db->quote($username->value, 'text'),
			SwatDB::equalityOperator($this->id, true),
			$this->app->db->quote($this->id, 'integer')));

		if (count($query) > 0) {
			$msg = new SwatMessage(
				Admin::_('Username already exists and must be unique.'),
				SwatMessage::ERROR);

			$username->addMessage($msg);
		}

		if ($this->ui->getWidget('confirm_password_field')->hasMessage() ||
			$this->ui->getWidget('password')->hasMessage())
			$this->ui->getWidget('password_disclosure')->open = true;
	}

	// }}}
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array('username', 'name', 'enabled'));

		$password = $this->ui->getWidget('password');
		if ($password->value !== null) {
			$values['password'] = md5($password->value);
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
			'usernum', $this->id, 'groupnum', $group_list->values,
			'AdminGroup', 'id');
		
		$msg = new SwatMessage(
			sprintf(Admin::_('User “%s” has been saved.'),
			$values['username']), SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);	
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
				sprintf(Admin::_("User with id '%s' not found."), $this->id));

		$this->ui->setValues(get_object_vars($row));
		
		$group_list = $this->ui->getWidget('groups');
		$group_list->values = SwatDB::queryColumn($this->app->db, 
			'AdminUserAdminGroupBinding', 'groupnum', 'usernum', $this->id);
	}

	// }}}
}

?>
