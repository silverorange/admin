<?php

require_once 'Admin/Admin/DBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for Admin user profile
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminProfile extends AdminDBEdit
{
	private $fields;

	public function init()
	{
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/Admin/profile.xml');

		$this->navbar->popElements();

		$confirm = $this->ui->getWidget('confirmpassword');
		$confirm->password_widget = $this->ui->getWidget('password');;
	}
	
	public function initDisplay()
	{
		$form = $this->ui->getWidget('edit_form');
		$form->action = $this->source;

		if (!$form->hasBeenProcessed())
			$this->loadData(null);

		$this->displayInitMessages();
	}


	protected function relocate()
	{
		$this->app->relocate('');
	}

	protected function saveDBData($id)
	{
		$name = $this->ui->getWidget('name');
		$values = array('name' => $name->value);
		
		$password = $this->ui->getWidget('password');
		if ($password->value !== null) {
			$values['password'] = md5($password->value);
		}

		SwatDB::updateRow($this->app->db, 'adminusers', array_keys($values),
			$values, 'integer:userid', $_SESSION['userID']);

		$_SESSION['name'] = $values['name'];

		$msg = new SwatMessage(Admin::_('Your user profile has been updated.'));
		$this->app->messages->add($msg);	
	}

	protected function loadDBData($id)
	{
		$row = SwatDB::queryRow($this->app->db, 'adminusers', 
			array('name'), 'integer:userid', $_SESSION['userID']);

		$this->ui->setValues(get_object_vars($row));
	}
}

?>
