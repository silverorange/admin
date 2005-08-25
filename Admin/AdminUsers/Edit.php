<?php

require_once 'Admin/Admin/DBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for AdminUsers component
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminUsersEdit extends AdminDBEdit
{
	private $fields;

	public function init()
	{
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminUsers/edit.xml');

		$this->fields = array('username', 'name', 'boolean:enabled');
		
		$group_list = $this->ui->getWidget('groups');
		$group_list->options = SwatDB::getOptionArray($this->app->db, 
			'admingroups', 'title', 'groupid', 'title');
		
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

	protected function processPage($id)
	{
		$username = $this->ui->getWidget('username');

		$query = SwatDB::query($this->app->db, sprintf('select username from
			adminusers where username = %s and userid %s %s',
			$this->app->db->quote($username->value, 'text'),
			SwatDB::equalityOperator($id, true),
			$this->app->db->quote($id, 'integer')));

		if ($query->numRows()) {
			$msg = new SwatMessage(Admin::_('Username already exists and must be unique.'), SwatMessage::ERROR);
			$shortname->addMessage($msg);
		}
	}

	protected function saveDBData($id)
	{
		$values = $this->ui->getValues(array('username', 'name', 'enabled'));

		$password = $this->ui->getWidget('password');
		if ($password->value !== null) {
			$values['password'] = md5($password->value);
			$this->fields[] = 'password';
		}

		if ($id == 0)
			$id = SwatDB::insertRow($this->app->db, 'adminusers', $this->fields,
				$values, 'integer:userid');
		else
			SwatDB::updateRow($this->app->db, 'adminusers', $this->fields,
				$values, 'integer:userid', $id);

		$group_list = $this->ui->getWidget('groups');

		SwatDB::updateBinding($this->app->db, 'adminuser_admingroup', 
			'usernum', $id, 'groupnum', $group_list->values, 'admingroups', 'groupid');
		
		$msg = new SwatMessage(sprintf(Admin::_('User "%s" has been saved.'), $values['username']), SwatMessage::NOTIFICATION);
		$this->app->messages->add($msg);	
	}

	protected function loadDBData($id)
	{
		$row = SwatDB::queryRow($this->app->db, 'adminusers', 
			$this->fields, 'integer:userid', $id);

		if ($row === null)
			return $this->app->replacePageNoAccess();

		$this->ui->setValues(get_object_vars($row));
		
		$group_list = $this->ui->getWidget('groups');
		$group_list->values = SwatDB::queryColumn($this->app->db, 
			'adminuser_admingroup', 'groupnum', 'usernum', $id);
	}
}

?>
