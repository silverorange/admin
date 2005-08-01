<?php

require_once 'Admin/AdminPage.php';
require_once 'Admin/AdminUI.php';
require_once 'Swat/SwatMessage.php';

/**
 * Administrator login page
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminLogin extends AdminPage
{
	public function init()
	{
		$this->layout = 'login';

		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/Admin/login.xml');

		$frame = $this->ui->getWidget('login_frame');
		$frame->title = $this->app->title;

		$username = $this->ui->getWidget('username');
		if (isset($_COOKIE[$this->app->id.'_username']))
			$username->value = $_COOKIE[$this->app->id.'_username'];
		
		$form = $this->ui->getWidget('login_form');
		$form->action = $this->app->getUri();
	}

	public function display()
	{
		parent::display();
		$this->displayJavascript();
	}

	public function process()
	{
		$form = $this->ui->getWidget('login_form');

		if ($form->process()) {
			if (!$form->hasMessage()) {
				$username = $this->ui->getWidget('username');
				$password = $this->ui->getWidget('password');
				$logged_in = $this->app->login($username->value, $password->value);
				
				if ($logged_in)
					$this->app->relocate($this->app->getUri());
				else {
					$frame = $this->ui->getWidget('login_frame');
					$msg = new SwatMessage(Admin::_('Login failed'), SwatMessage::USER_ERROR);
					$frame->addMessage($msg);
				}
			}
		}
	}

	private function displayJavascript()
	{
		if (isset($_COOKIE[$this->app->id.'_username']))
			$username = $_COOKIE[$this->app->id.'_username'];
		else
			$username = '';
		
		echo '<script type="text/javascript">';
		require_once('Admin/javascript/admin-login.js');
		echo "\n adminLogin('username', 'password', '{$username}');";
		echo '</script>';
	}
}

?>
