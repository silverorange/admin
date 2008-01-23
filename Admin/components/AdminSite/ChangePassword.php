<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Admin/dataobjects/AdminUser.php';
require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/layouts/AdminLoginLayout.php';
require_once 'Swat/SwatMessage.php';

/**
 * Force change password page after initial login
 *
 * @package   Admin
 * @copyright 2005-2007 silverorange
 */
class AdminAdminSiteChangePassword extends AdminPage
{
	// init phase
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new AdminLoginLayout($this->app,
			'Admin/layouts/xhtml/change-password.php');
	}

	// }}}
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML(dirname(__FILE__).'/change-password.xml');

		$confirm = $this->ui->getWidget('confirm_password');
		$confirm->password_widget = $this->ui->getWidget('password');

		$form = $this->ui->getWidget('change_password_form');
		$form->action = 'AdminSite/ChangePassword';

		// remember where we came from
		$form->addHiddenField('relocate_uri', $this->app->getUri());
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		$form = $this->ui->getWidget('change_password_form');
		if ($form->isProcessed()) {
			$this->validatePasswords();
			if (!$form->hasMessage()) {
				$password = $this->ui->getWidget('password')->value;

				$user = $this->app->session->user;
				$user->setPassword($password);
				$user->force_change_password = false;
				$user->save();

				$this->app->session->login($user->email, $password);

				$message = new SwatMessage(
					Admin::_('Your password has been updated.'));

				$this->app->messages->add($message);

				// go back where we came from
				$uri = $form->getHiddenField('relocate_uri');
				$this->app->relocate($uri);
			}
		}
	}

	// }}}
	// {{{ protected function validatePasswords()

	protected function validatePasswords()
	{
		$user = $this->app->session->user;

		$old_password = $this->ui->getWidget('old_password')->value;
		$new_password = $this->ui->getWidget('password')->value;

		if ($old_password === null || $new_password === null)
			return;

		// make sure old password is not the same as new password
		if ($old_password == $new_password) {
			$message = new SwatMessage(Admin::_('Your new password can not be '.
				'the same as your old password'), SwatMessage::ERROR);

			$this->ui->getWidget('password')->addMessage($message);
		}

		// make sure old password is correct
		if (!$user->validatePassword($old_password)) {
			$message = new SwatMessage(
				Admin::_('Your old password is not correct'),
				SwatMessage::ERROR);

			$this->ui->getWidget('old_password')->addMessage($message);
		}
	}

	// }}}
}

?>
