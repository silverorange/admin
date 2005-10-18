<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for AdminUsers component
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminUsersEdit extends AdminDBEdit
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
			'admingroups', 'title', 'id', 'title');
		
		$confirm = $this->ui->getWidget('confirm_password');
		$confirm->password_widget = $this->ui->getWidget('password');;
		
		$id = SwatApplication::initVar('id');
		if ($id === null) {
			$this->ui->getWidget('password')->required = true;
			$confirm->required = true;
			
			$this->ui->getWidget('confirm_password_field')->note = null;
			$this->ui->getWidget('password_disclosure')->open = true;
			$this->ui->getWidget('password_disclosure')->title = Admin::_('Set Password');
		}
	}

	// }}}

	// process phase
	// {{{ protected function validate()

	protected function validate($id)
	{
		$username = $this->ui->getWidget('username');

		$query = SwatDB::query($this->app->db, sprintf('select username from
			adminusers where username = %s and id %s %s',
			$this->app->db->quote($username->value, 'text'),
			SwatDB::equalityOperator($id, true),
			$this->app->db->quote($id, 'integer')));

		if ($query->getCount() > 0) {
			$msg = new SwatMessage(Admin::_('Username already exists and must be unique.'), SwatMessage::ERROR);
			$username->addMessage($msg);
		}
	}

	// }}}
	// {{{ protected function saveDBData()

	protected function saveDBData($id)
	{
		$values = $this->ui->getValues(array('username', 'name', 'enabled'));

		$password = $this->ui->getWidget('password');
		if ($password->value !== null) {
			$values['password'] = md5($password->value);
			$this->fields[] = 'password';
		}

		if ($id === null)
			$id = SwatDB::insertRow($this->app->db, 'adminusers', $this->fields,
				$values, 'integer:id');
		else
			SwatDB::updateRow($this->app->db, 'adminusers', $this->fields,
				$values, 'integer:id', $id);

		$group_list = $this->ui->getWidget('groups');

		SwatDB::updateBinding($this->app->db, 'adminuser_admingroup', 
			'usernum', $id, 'groupnum', $group_list->values, 'admingroups', 'id');
		
		$msg = new SwatMessage(
			sprintf(Admin::_('User “%s” has been saved.'),
			$values['username']), SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);	
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData($id)
	{
		$row = SwatDB::queryRowFromTable($this->app->db, 'adminusers', 
			$this->fields, 'integer:id', $id);

		if ($row === null)
			return $this->app->replacePageNoAccess();

		$this->ui->setValues(get_object_vars($row));
		
		$group_list = $this->ui->getWidget('groups');
		$group_list->values = SwatDB::queryColumn($this->app->db, 
			'adminuser_admingroup', 'groupnum', 'usernum', $id);
	}

	// }}}
}

?>
