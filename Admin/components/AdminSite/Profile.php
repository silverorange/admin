<?php

require_once 'Admin/pages/AdminObjectEdit.php';
require_once 'Admin/dataobjects/AdminUser.php';

/**
 * Edit page for the current admin user profile
 *
 * @package   Admin
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @copyright 2005-2014 silverorange
 */
class AdminAdminSiteProfile extends AdminObjectEdit
{
	// {{{ protected function getObjectClass()

	protected function getObjectClass()
	{
		return 'AdminUser';
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Admin/components/AdminSite/profile.xml';
	}

	// }}}
	// {{{ protected function getObjectPropertyWidgetMapping()

	protected function getObjectPropertyWidgetMapping()
	{
		return array(
			'email',
			'name',
		);
	}

	// }}}

	// init phase
	// {{{ protected function initObject()

	protected function initObject()
	{
		// Set the id so the edit page knows it's an edit.
		$this->id = $this->app->session->getUserId();

		// Bypass all AdminObjectEdit loading.
		$this->data_object = $this->app->session->user;
	}

	// }}}
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->initPasswordWidgets();
	}

	// }}}
	// {{{ protected function initPasswordWidgets()

	protected function initPasswordWidgets()
	{
		$confirm = $this->ui->getWidget('confirm_password');
		$confirm->password_widget = $this->ui->getWidget('new_password');
	}

	// }}}

	// process phase
	// {{{ protected function validate()

	protected function validate()
	{
		parent::validate();

		$new_password = $this->ui->getWidget('new_password')->value;
		$old_password = $this->ui->getWidget('old_password')->value;

		if ($new_password != '') {
			$crypt = $this->app->getModule('SiteCryptModule');

			$password_hash = $this->app->session->user->password;
			$password_salt = $this->app->session->user->password_salt;

			if (!$crypt->verifyHash(
					$old_password,
					$password_hash,
					$password_salt
				)) {

				$this->ui->getWidget('old_password')->addMessage(
					new SwatMessage(
						Admin::_(
							'%1$s is incorrrect. Please check your %1$s and '.
							'try again. Passwords are case sensitive.'
						),
						'error'
					)
				);
			}
		}
	}

	// }}}
	// {{{ protected function updateObject()

	protected function updateObject()
	{
		parent::updateObject();

		$this->updatePassword();
	}

	// }}}
	// {{{ protected function updatePassword()

	protected function updatePassword()
	{
		$new_password = $this->ui->getWidget('new_password')->value;

		if ($new_password != '') {
			$crypt = $this->app->getModule('SiteCryptModule');
			$this->getObject()->setPasswordHash(
				$crypt->generateHash($new_password)
			);
		}
	}

	// }}}
	// {{{ protected function getSavedMessageText()

	protected function getSavedMessageText()
	{
		return Admin::_('Your user profile has been updated.');
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$this->app->relocate('.');
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

		if ($old_password->hasMessage() ||
			$new_password->hasMessage() ||
			$confirm_password->hasMessage()) {
			$this->ui->getWidget('change_password')->open = true;
		}
	}

	// }}}
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
		// skip the parent buildFrame()
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
