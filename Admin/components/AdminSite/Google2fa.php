<?php

use PragmaRX\Google2FA\Google2FA;

/**
 * Authenticate Google 2FA Token
 *
 * @package   Admin
 * @copyright 2022 silverorange
 */
class AdminAdminSiteGoogle2fa extends AdminPage
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

		$this->ui->loadFromXML(__DIR__.'/google_2fa.xml');

		$form = $this->ui->getWidget('google_2fa_form');
		$form->action = 'AdminSite/Google2fa';

		// remember where we came from
		$form->addHiddenField('relocate_uri', $this->app->getUri());

		$user = $this->app->session->user;
		if ($user->isGoogle2faAuthenticated()) {
			$this->app->relocate('./');
		}
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		$form = $this->ui->getWidget('google_2fa_form');
		if ($form->isProcessed()) {
			$this->validate2fa();
			if (!$form->hasMessage()) {
				$this->app->session->user->setGoogle2faAuthenticated();

				// go back where we came from
				$uri = $form->getHiddenField('relocate_uri');
				$this->app->relocate($uri);
			}
		}
	}

	// }}}
	// {{{ protected function validate2fa()

	protected function validate2fa()
	{
		$google2fa = new Google2FA();
		$verified = $google2fa->verifyKey(
			$this->app->session->user->google_2fa_secret,
			$this->ui->getWidget('google_2fa')->value
		);

		if (!$verified) {
			$this->ui->getWidget('google_2fa')->addMessage(
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
}

?>
