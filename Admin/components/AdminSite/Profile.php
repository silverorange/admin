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
			'two_fa_enabled'
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

		$this->build2fa();
	}

	// }}}
	// {{{ protected function build2Fa()

	protected function build2Fa()
	{
		if ($this->app->is2FaEnabled()) {
			$two_fa = new TwoFactorAuth();
			if ($this->data_object->two_fa_secret === null) {
				$two_fa_secret = $two_fa->createSecret();
				$this->data_object->two_fa_secret = $two_fa_secret;
				$this->data_object->save();
			}

			if ($this->data_object->two_fa_enabled) {
				$this->ui->getWidget('two_fa_enabled_note')->visible = true;
			} else {
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
				/*$img_tag->src = 'data:image/png;base64, '.base64_encode(
					$writer->writeString($qr_code_url)
				);*/

				$this->ui->getWidget('two_fa_image')->content = $img_tag;
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
}

?>
