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
		$form->action = $_SERVER['REQUEST_URI'];
	}

	public function display() {
		$root = $this->layout->getRoot();
		$root->display();
	}

	public function process() {
		$root = $this->layout->getRoot();
		$root->process();

		$username = $this->layout->getWidget('username');
		$password = $this->layout->getWidget('password');

		$this->app->login($username, $password);
	}

	public function getLayout() {
		return 'login';
	}
}

?>
