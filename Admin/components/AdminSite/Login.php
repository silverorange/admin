<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/layouts/AdminLayout.php';
require_once 'Admin/AdminUI.php';
require_once 'Swat/SwatMessage.php';

/**
 * Administrator login page
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
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
		return new SiteLayout($this->app, 'Admin/layouts/xhtml/login.php');
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/login.xml');
		$this->ui->getWidget('login_form')->addJavaScript(
			'packages/admin/javascript/admin-login.js',
			Admin::PACKAGE_ID);

		$frame = $this->ui->getWidget('login_frame');
		$frame->title = $this->app->title;

		$username = $this->ui->getWidget('username');
		if (isset($this->app->cookie->username))
			$username->value = $this->app->cookie->username;

		$form = $this->ui->getWidget('login_form');
		$form->action = $this->app->getUri();
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		$form = $this->ui->getWidget('login_form');

		if ($form->isProcessed() && !$form->hasMessage()) {
			$username = $this->ui->getWidget('username')->value;
			$password = $this->ui->getWidget('password')->value;
			$logged_in = $this->app->session->login($username, $password);

			if ($logged_in) {
				$this->app->relocate($this->app->getUri());
			} else {
				$message_display = $this->ui->getWidget('message_display');
				$msg = new SwatMessage(Admin::_('Login failed'), 
					SwatMessage::ERROR);

				$msg->secondary_content = 
					Admin::_('Check your password and try again.');

				$message_display->add($msg);
				$this->login_error = true;
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
		$username = (isset($this->app->cookie->username)) ?
			$this->app->cookie->username : '';

		$username = str_replace("'", "\\'", $username);
		$username = str_ireplace('</script>', "</script' + '>", $username);

		$login_error = ($this->login_error) ? 'true' : 'false';

		echo '<script type="text/javascript">';
		echo "\nAdminLogin('username', 'password', 'login_button', ".
			"'{$username}', {$login_error});";

		echo '</script>';
	}

	// }}}
}

?>
