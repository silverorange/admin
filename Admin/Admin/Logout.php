<?php

require_once('Admin/AdminPage.php');

class AdminLogout extends AdminPage {

	public function init() {
		$this->layout = 'login';
	}

	public function display() {

	}

	public function process() {
		$this->app->logout();
		$this->app->relocate($this->app->basehref);
	}

}

?>
