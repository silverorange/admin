<?php

require_once 'Admin/dataobjects/AdminUser.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'Swat/SwatString.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for the current admin user profile
 *
 * @package   Admin
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @copyright 2005-2007 silverorange
 */
class AdminAdminSiteProfile extends AdminDBEdit
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML(dirname(__FILE__).'/admin-site-profile.xml');

		$confirm = $this->ui->getWidget('confirm_password');
		$confirm->password_widget = $this->ui->getWidget('password');

		// We only need to set the id so the edit page doesn't think this is
		// an 'add' instead of an edit.
		$this->id = $this->app->session->getUserId();
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$user = $this->app->session->user;

		$user->name = $this->ui->getWidget('name')->value;
		$user->email = $this->ui->getWidget('email')->value;

		$password = $this->ui->getWidget('password');
		if ($password->value !== null) {
			$salt = SwatString::getSalt(AdminUser::PASSWORD_SALT_LENGTH);
			$user->password_salt = $salt;
			$user->password = md5($password->value.$salt);
		}

		$user->save();

		$message = new SwatMessage(
			Admin::_('Your user profile has been updated.'));

		$this->app->messages->add($message);
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$this->app->relocate('');
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
		$this->ui->setValues(get_object_vars($this->app->session->user));

		// don't set the password field to the hashed password
		$this->ui->getWidget('password')->value = null;
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
