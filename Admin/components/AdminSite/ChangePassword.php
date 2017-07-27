<?php

/**
 * Force change password page after initial login
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 */
class AdminAdminSiteChangePassword extends AdminPage
{
	// init phase
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new AdminLoginLayout($this->app, AdminLoginTemplate::class);
	}

	// }}}
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML(__DIR__.'/change-password.xml');

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

		$crypt = $this->app->getModule('SiteCryptModule');

		$form = $this->ui->getWidget('change_password_form');
		if ($form->isProcessed()) {
			$this->validatePasswords();
			if (!$form->hasMessage()) {
				$password = $this->ui->getWidget('password')->value;

				$user = $this->app->session->user;
				$user->setPasswordHash($crypt->generateHash($password));
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
				'the same as your old password'), 'error');

			$this->ui->getWidget('password')->addMessage($message);
		}

		$crypt = $this->app->getModule('SiteCryptModule');

		$password_hash = $user->password;
		$password_salt = $user->password_salt;

		// make sure old password is correct
		if (!$crypt->verifyHash(
				$old_password,
				$password_hash,
				$password_salt
			)) {

			$this->ui->getWidget('old_password')->addMessage(
				new SwatMessage(
					Admin::_('Your old password is not correct'),
					'error'
				)
			);
		}
	}

	// }}}

	// finalize phase
	// {{{ public function finalize

	public function finalize()
	{
		parent::finalize();

		$this->layout->addHtmlHeadEntry(
			'packages/admin/styles/admin-change-password-page.css'
		);
	}

	// }}}
}

?>
