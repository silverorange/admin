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

		$form = $this->ui->getWidget('loginform');
		$form->action = $this->app->uri;
	}

	public function display() {
		//$form = $this->ui->getWidget('loginform');
		//$error_messages = $form->gatherErrorMessages();
		
		$root = $this->ui->getRoot();
		$root->display();
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

}

?>
