<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for the current admin user profile
 *
 * @package   Admin
 * @copyright 2004-2006 silverorange
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
		parent::initInternal();

		$this->ui->loadFromXML(dirname(__FILE__).'/profile.xml');

		$confirm = $this->ui->getWidget('confirmpassword');
		$confirm->password_widget = $this->ui->getWidget('password');

		$this->id = SiteApplication::initVar('user_id',
			null, SiteApplication::VAR_SESSION);
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$name = $this->ui->getWidget('name');
		$values = array('name' => $name->value);

		$password = $this->ui->getWidget('password');
		if ($password->value !== null) {
			$values['password'] = md5($password->value);
		}

		SwatDB::updateRow($this->app->db, 'adminusers', array_keys($values),
			$values, 'integer:id', $this->id);

		$_SESSION['name'] = $values['name'];

		$msg = new SwatMessage(Admin::_('Your user profile has been updated.'));
		$this->app->messages->add($msg);
	}

	// }}}

	// build phase
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
	}

	// }}}
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$row = SwatDB::queryRowFromTable($this->app->db, 'adminusers', 
			array('name'), 'integer:id', $this->id);

		$this->ui->setValues(get_object_vars($row));
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		$this->navbar->popEntry();
		$this->navbar->createEntry(Admin::_('Login Settings'));
	}

	// }}}
}

?>
