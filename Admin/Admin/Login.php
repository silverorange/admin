<?php

require_once("Admin/AdminPage.php");
require_once('Swat/SwatLayout.php');

class AdminLogin extends AdminPage {

	private $layout;

	public function init($app) {
		$this->layout = new SwatLayout('Admin/Admin/login.xml');

		$frame = $this->layout->getWidget('frame');
		$frame->title = $app->title;

		$form = $this->layout->getWidget('loginform');
		$form->action = $_SERVER['REQUEST_URI'];
	}

	public function display($app) {
		$root = $this->layout->getRoot();
		$root->display();
	}

	public function process($app) {
		$root = $this->layout->getRoot();
		$root->process();
	}

	public function getLayout() {
		return 'login';
	}
}

?>
