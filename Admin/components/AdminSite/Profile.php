<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for Admin user profile
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminSiteProfile extends AdminDBEdit
{
	// {{{ private properties

	private $fields;

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$this->app->relocate('');
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/profile.xml');

		$this->navbar->popEntry();
		$this->navbar->createEntry(Admin::_('My Profile'));

		$confirm = $this->ui->getWidget('confirmpassword');
		$confirm->password_widget = $this->ui->getWidget('password');;
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData($id)
	{
		$name = $this->ui->getWidget('name');
		$values = array('name' => $name->value);

		$password = $this->ui->getWidget('password');
		if ($password->value !== null) {
			$values['password'] = md5($password->value);
		}

		SwatDB::updateRow($this->app->db, 'adminusers', array_keys($values),
			$values, 'integer:id', $_SESSION['user_id']);

		$_SESSION['name'] = $values['name'];

		$msg = new SwatMessage(Admin::_('Your user profile has been updated.'));
		$this->app->messages->add($msg);
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		$form = $this->ui->getWidget('edit_form');
		$form->action = $this->source;

		if (!$form->isProcessed())
			$this->loadData(null);

		$this->buildMessages();
	}

	// }}}
	// {{{ protected function loadDBData()

	protected function loadDBData($id)
	{
		$row = SwatDB::queryRowFromTable($this->app->db, 'adminusers', 
			array('name'), 'integer:id', $_SESSION['user_id']);

		$this->ui->setValues(get_object_vars($row));
	}

	// }}}
}

?>
