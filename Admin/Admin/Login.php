<?php

require_once("Admin/AdminPage.php");
require_once('Swat/SwatLayout.php');

class AdminLogin extends AdminPage {

	private $layout;

	public function init() {
		$this->layout = new SwatLayout('Admin/Admin/login.xml');

		$frame = $this->layout->getWidget('frame');
		$frame->title = $this->app->title;

		$form = $this->layout->getWidget('loginform');
		$form->action = $this->app->uri;
	}

	public function display() {
		$root = $this->layout->getRoot();
		$root->display();
	}

	public function process() {
		$form = $this->layout->getWidget('loginform');

		if ($form->process()) {
			if (!$form->hasErrorMessage()) {
				$username = $this->layout->getWidget('username');
				$password = $this->layout->getWidget('password');

				$this->app->login($username, $password);
				$this->app->relocate($this->app->uri);
			}
		}
	}

	public function getLayout() {
		return 'login';
	}
}

?>
