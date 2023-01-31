<?php

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
		$success = $this->app->session->loginWithTwoFactorAuthentication(
			$this->ui->getWidget('two_fa_token')->value
		);

		if (!$success) {
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
