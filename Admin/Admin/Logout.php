<?php

require_once("Admin/AdminPage.php");
require_once('Swat/SwatLayout.php');

class AdminLogout extends AdminPage {

	public function init() {

	}

	public function display() {

	}

	public function process() {
		$this->app->logout();

		// TODO: use a relocate function here
		header('Location: '.$this->app->basehref);
		exit();
	}

	public function getLayout() {
		return 'login';
	}
}

?>
