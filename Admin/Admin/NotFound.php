<?php

require_once('Admin/AdminPage.php');
require_once('Swat/SwatMessageBox.php');

/**
 * Administrator Not Found page
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminNotFound extends AdminPage {

	private $message = null;

	public function init() {
	}

	public function display() {
		$box = new SwatMessageBox();
		$box->title = 'Not Found';

		if ($this->message !== null)
			$box->messages = array($this->message); 

		$box->display();
	}

	public function process() {

	}

	public function setMessage($msg) {
		$this->message = $msg;
	}
}

?>
