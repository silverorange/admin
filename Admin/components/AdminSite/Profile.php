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

		$this->ui->loadFromXML(__DIR__.'/profile.xml');

		$confirm = $this->ui->getWidget('confirm_password');
		$confirm->password_widget = $this->ui->getWidget('new_password');

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

		$new_password = $this->ui->getWidget('new_password');
		if ($new_password->value !== null) {
			$user->setPassword($new_password->value);
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
	// {{{ protected function validate()

	protected function validate()
	{
		parent::validate();
		$user = $this->app->session->user;
		$new_password = $this->ui->getWidget('new_password')->value;
		$old_password = $this->ui->getWidget('old_password')->value;
		if ($new_password !== null && !$user->validatePassword($old_password)) {
			$message = new SwatMessage(
				Admin::_('%1$s is incorrrect. Please check your %1$s and try '.
					'again. Passwords are case sensitive.'),
				'error');

			$this->ui->getWidget('old_password')->addMessage($message);
		}
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$old_password     = $this->ui->getWidget('old_password');
		$new_password     = $this->ui->getWidget('new_password');
		$confirm_password = $this->ui->getWidget('confirm_password');

		if ($old_password->hasMessage() || $new_password->hasMessage() ||
			$confirm_password->hasMessage()) {
			$this->ui->getWidget('change_password')->open = true;
		}
	}

	// }}}
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
	}

	// }}}
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->app->session->user));
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
