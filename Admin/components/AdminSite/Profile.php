<?php

use PragmaRX\Google2FA\Google2FA;

/**
 * Edit page for the current admin user profile
 *
 * @package   Admin
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @copyright 2005-2016 silverorange
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
		return __DIR__.'/profile.xml';
	}

	// }}}
	// {{{ protected function getObjectUiValueNames()

	protected function getObjectUiValueNames()
	{
		return array(
			'email',
			'name',
			'google_2fa_enabled'
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
	// {{{ protected function getSavedMessagePrimaryContent()

	protected function getSavedMessagePrimaryContent()
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

		$this->buildGoogle2fa();
	}

	// }}}
	// {{{ protected function buildGoogle2fa()

	protected function buildGoogle2fa()
	{
		if ($this->app->isGoogle2faEnabled()) {
			$google2fa = new Google2FA();
			if ($this->data_object->google_2fa_secret === null) {
				$google2fa_secret = $google2fa->generateSecretKey();
				$this->data_object->google_2fa_secret = $google2fa_secret;
				$this->data_object->save();
			}

			if ($this->data_object->google_2fa_enabled) {
				$this->ui->getWidget('google_2fa_enabled_note')->visible = true;
			} else {
				$qr_code_url = $google2fa->getQRCodeUrl(
					$this->app->config->admin->google_2fa_domain,
					$this->data_object->email,
					$this->data_object->google_2fa_secret
				);

				$img_tag = new SwatHtmlTag('img');
				$img_tag->src = 'https://chart.googleapis.com/chart'.
					'?chs=400x400&chld=M|0&cht=qr&chl='.urlencode($qr_code_url);

				$this->ui->getWidget('google_2fa_image')->content = $img_tag;
				$this->ui->getWidget('google_2fa')->visible = true;
			}
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
