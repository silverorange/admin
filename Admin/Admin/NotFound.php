<?php

require_once("Admin/AdminPage.php");

class AdminNotFound extends AdminPage {

	public function init($app) {

	}

	public function display($app) {
		echo 'Not Found';
	}

	public function process($app) {

	}
}

?>
