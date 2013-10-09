<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/layouts/AdminLoginLayout.php';
require_once 'Admin/AdminUI.php';
require_once 'Swat/SwatMessage.php';

/**
 * Administrator login page
 *
 * @package   Admin
 * @copyright 2005-2012 silverorange
 */
class AdminAdminSiteLogin extends AdminPage
{
	// {{{ protected properties

	/**
	 * Whether or not there was an error in the login information entered by
	 * the user
	 *
	 * @var boolean
	 */
	protected $login_error = false;

	// }}}
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new AdminLoginLayout($this->app,
			'Admin/layouts/xhtml/login.php');
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(__DIR__.'/login.xml');
		$this->ui->getWidget('login_form')->addJavaScript(
			'packages/admin/javascript/admin-login.js',
			Admin::PACKAGE_ID);

		$frame = $this->ui->getWidget('login_frame');
		$frame->title = $this->app->title;

		$email = $this->ui->getWidget('email');
		try {
			if (isset($this->app->cookie->email))
				$email->value = $this->app->cookie->email;

		} catch (SiteCookieException $e) {
			$this->app->cookie->removeCookie('email', '/');
		}

		$form = $this->ui->getWidget('login_form');
		$form->action = $this->app->getUri();

		if (!$this->app->config->admin->allow_reset_password) {
			$this->ui->getWidget('forgot_container')->visible = false;
			$this->ui->getWidget('password_container')->classes[] =
				'no-reset-password';
		}
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		$form = $this->ui->getWidget('login_form');

		if ($form->isProcessed() && !$form->hasMessage()) {
			$email = $this->ui->getWidget('email')->value;
			$password = $this->ui->getWidget('password')->value;
			$logged_in = $this->app->session->login($email, $password);

			if ($logged_in) {
				$this->app->relocate($this->app->getUri());
			} else {
				if (isset($this->app->session->user) &&
					$this->app->session->user->force_change_password) {
					$this->app->replacePage('AdminSite/ChangePassword');
				} else {
					$message_display = $this->ui->getWidget('message_display');
					$message = new SwatMessage(Admin::_('Login failed'),
						'error');

					$message->secondary_content =
						Admin::_('Check your password and try again.');

					$message_display->add($message);
					$this->login_error = true;
				}
			}
		}
	}

	// }}}

	// build phase
	// {{{ protected function display()

	protected function display()
	{
		parent::display();
		$this->displayJavaScript();
	}

	// }}}
	// {{{ private function displayJavaScript()

	private function displayJavaScript()
	{
		try {
			$email = (isset($this->app->cookie->email)) ?
				$this->app->cookie->email : '';
		} catch (SiteCookieException $e) {
			$this->app->cookie->removeCookie('email', '/');
			$email = '';
		}

		$email = str_replace("'", "\\'", $email);
		$email = str_ireplace('</script>', "</script' + '>", $email);

		$login_error = ($this->login_error) ? 'true' : 'false';

		echo '<script type="text/javascript">';
		echo "\nAdminLogin('email', 'password', 'login_button', ".
			"'{$email}', {$login_error});";

		echo '</script>';
	}

	// }}}

	// finalize phase
	// {{{ public function finalize

	public function finalize()
	{
		parent::finalize();

		$this->layout->addHtmlHeadEntry(
			'packages/admin/styles/admin-login-page.css',
			Admin::PACKAGE_ID
		);
	}

	// }}}
}

?>
