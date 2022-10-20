<?php

/**
 * Edit page for the current admin user profile
 *
 * @package   Admin
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @copyright 2005-2022 silverorange
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

		if ($this->ui->getWidget('two_fa_token')->value !== null) {
			$this->validate2Fa();
		}
	}

	// }}}
	// {{{ protected function validate2Fa()

	protected function validate2Fa()
	{
		$two_factor_authentication = new AdminTwoFactorAuthentication();
		$success = $two_factor_authentication->validateToken(
			$this->app->session->user->two_fa_secret,
			$this->ui->getWidget('two_fa_token')->value,
			$this->app->session->user->two_fa_timeslice
		);

		if ($success) {
			// save the new timestamp
			$this->app->session->user->save();
		} else {
			$this->ui->getWidget('two_fa_token')->addMessage(
				new SwatMessage(
					Admin::_(
						'Your two factor authentication token doesn’t '.
						'match. Try again, or contact support for help.'
					),
					'error'
				)
			);
		}
	}

	// }}}
	// {{{ protected function updateObject()

	protected function updateObject()
	{
		parent::updateObject();

		$this->updatePassword();

		if ($this->ui->getWidget('two_fa_token')->value !== null) {
			$this->app->session->user->two_fa_enabled = true;
			$this->app->session->user->set2FaAuthenticated();
		}
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

		$this->build2fa();
	}

	// }}}
	// {{{ protected function build2Fa()

	protected function build2Fa()
	{
		if ($this->app->is2FaEnabled()) {
			if ($this->data_object->two_fa_enabled) {
				$this->ui->getWidget('two_fa_enabled_note')->visible = true;
			} else {
				$two_factor_authentication = new AdminTwoFactorAuthentication();

				$form = $this->ui->getWidget('edit_form');
				if (!$form->isSubmitted()) {
					// Generate a new secret key each time the page loads so
					// that someone doesn't steal the secret code, then later
					// this user turns on 2FA, and  then the intruder would
					// have the secret key from before.
					$secret = $two_factor_authentication->getNewSecret();
					$this->data_object->two_fa_secret = $secret;
					$this->data_object->save();
				}

				$qr_code_url = $two_factor_authentication->getQrCodeDataUri(
					sprintf(
						'%s (%s)',
						$this->app->config->site->title,
						$this->data_object->email
					),
					$this->data_object->two_fa_secret
				);

				$img_tag = new SwatHtmlTag('img');
				$img_tag->alt = Admin::_('Two Factor Authentication QR Code');
				$img_tag->src = $qr_code_url;

				$p_tag = new SwatHtmlTag('p');
				$p_tag->class = 'admin-two-factor-secret';
				$p_tag->setContent($this->data_object->two_fa_secret);

				ob_start();
				$img_tag->display();
				$p_tag->display();
				$this->ui->getWidget('two_fa_image')->content = ob_get_clean();
				$this->ui->getWidget('two_fa')->visible = true;
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

	// finalize phase
	// {{{ public function finalize

	public function finalize()
	{
		parent::finalize();

		$this->layout->addHtmlHeadEntry(
			'packages/admin/styles/admin-profile.css'
		);
	}

	// }}}
}

?>
