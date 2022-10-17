<?php

use RobThree\Auth\TwoFactorAuth;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

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
		// strip all non numeric characters like spaces and dashes that people
		// might enter (e.g. Authy adds spaces for readability)
		$token = preg_replace(
			'/[^0-9]/',
			'',
			$this->ui->getWidget('two_fa_token')->value
		);

		// The timestamp is used to make sure this, or tokens before this,
		// can't be used to authenticate again. There's a "window" of token
		// use and without this, someone could capture the code, and re-use it.
		$two_fa = new TwoFactorAuth();
		$success = $two_fa->verifyCode(
			$this->app->session->user->two_fa_secret,
			$token,
			1,
			null,
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
			$two_fa = new TwoFactorAuth();
			if ($this->data_object->two_fa_enabled) {
				$this->ui->getWidget('two_fa_enabled_note')->visible = true;
			} else {
				// Generate a new secret key each time the page loads so that
				// someone doesn't steal the secret code, then later this
				// user turns on 2FA, and  then the intruder would have the
				// secret key from before.
				$two_fa_secret = $two_fa->createSecret();
				$this->data_object->two_fa_secret = $two_fa_secret;
				$this->data_object->save();

				$qr_code_url = $two_fa->getQRCodeImageAsDataUri(
					sprintf(
						'%s (%s)',
						$this->app->config->site->title,
						$this->data_object->email
					),
					$this->data_object->two_fa_secret,
					400
				);

				$writer = new Writer(
					new ImageRenderer(
						new RendererStyle(400),
						new ImagickImageBackEnd()
					)
				);

				$img_tag = new SwatHtmlTag('img');
				$img_tag->alt = Admin::_('Two Factor Authentication QR Code');
				$img_tag->src = $qr_code_url;

				$p_tag = new SwatHtmlTag('p');
				$p_tag->class = 'two-factor-secret';
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
