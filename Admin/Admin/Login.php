<?php

require_once('Admin/AdminPage.php');
require_once('Admin/AdminUI.php');

/**
 * Administrator login page
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminLogin extends AdminPage {

	private $ui;

	public function init() {
		$this->layout = 'login';

		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/Admin/login.xml');

		$frame = $this->ui->getWidget('frame');
		$frame->title = $this->app->title;

		$username = $this->ui->getWidget('username');
		if (isset($_COOKIE[$this->app->name.'_username']))
			$username->value = $_COOKIE[$this->app->name.'_username'];
		
		
		$form = $this->ui->getWidget('loginform');
		$form->action = $this->app->uri;
	}

	public function display() {
		//$form = $this->ui->getWidget('loginform');
		//$error_messages = $form->gatherErrorMessages();
		
		$root = $this->ui->getRoot();
		$root->display();

		$this->displayJavascript();
	}

	public function process() {
		$form = $this->ui->getWidget('loginform');

		if ($form->process()) {
			if (!$form->hasErrorMessage()) {
				$username = $this->ui->getWidget('username');
				$password = $this->ui->getWidget('password');
				$logged_in = $this->app->login($username->value, $password->value);
				
				if ($logged_in)
					$this->app->relocate($this->app->uri);
				else {
					$frame = $this->ui->getWidget('frame');
					$frame->addErrorMessage(_S("Login failed"));
				}
			}
		}
	}

	private function displayJavascript() {
		if (isset($_COOKIE[$this->app->name.'_username']))
			$username = $_COOKIE[$this->app->name.'_username'];
		else
			$username = '';
		
		echo '<script type="text/javascript">';
		require_once('Admin/javascript/admin-login.js');
		echo "\n adminLogin('username', 'password', '{$username}');";
		echo '</script>';
	}
}

?>
