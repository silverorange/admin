<?php

require_once('Admin/AdminPage.php');

/**
 * Administrator Not Found page
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminFront extends AdminPage {

	public function init() {
		//clear the navbar
		$this->navbar = new SwatNavBar();
	}

	public function display() {
		echo 'Welcome to the front page. Please make me better.';
	}

	public function process() {

	}
}

?>
