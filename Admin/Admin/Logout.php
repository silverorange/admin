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
		$this->app->relocate($this->app->basehref);
	}

	public function getLayout() {
		return 'login';
	}
}

?>
