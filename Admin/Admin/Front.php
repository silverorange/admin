<?php

require_once('Admin/AdminPage.php');
require_once('Admin/AdminUI.php');

/**
 * Administrator Not Found page
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminFront extends AdminPage {

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/Admin/front.xml');
		
		//clear the navbar
		$this->navbar = new SwatNavBar();
	}

	public function displayInit() {
		$this->displayInitMessages();
	}


	public function process() {

	}
}

?>
