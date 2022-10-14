<?php

use RobThree\Auth\TwoFactorAuth;

/**
 * Authenticate 2FA Token
 *
 * @package   Admin
 * @copyright 2022 silverorange
 */
class AdminAdminSiteTwoFactorAuthentication extends AdminPage
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

		$this->ui->loadFromXML(__DIR__.'/two_factor_authentication.xml');

		$form = $this->ui->getWidget('two_fa_form');
		$form->action = 'AdminSite/TwoFactorAuthentication';

		// remember where we came from
		$form->addHiddenField('relocate_uri', $this->app->getUri());

		$user = $this->app->session->user;
		if ($user->is2FaAuthenticated() || !$user->two_fa_enabled) {
			$this->app->relocate('./');
		}
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		$form = $this->ui->getWidget('two_fa_form');
		if ($form->isProcessed()) {
			if (!$form->hasMessage()) {
				$this->validate2Fa();
			}

			if (!$form->hasMessage()) {
				$this->app->session->user->set2FaAuthenticated();

				// go back where we came from
				$uri = $form->getHiddenField('relocate_uri');
				$this->app->relocate($uri);
			}
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

	// finalize phase
	// {{{ public function finalize

	public function finalize()
	{
		parent::finalize();

		$this->layout->addHtmlHeadEntry(
			'packages/admin/styles/admin-two-factor-authentication-page.css'
		);
	}

	// }}}
}

?>
