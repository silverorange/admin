<?php

require_once 'Admin/AdminPage.php';
require_once 'Swat/SwatMessageDisplay.php';

/**
 * Administrator Not Found page
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminNotFound extends AdminPage
{
	private $message = null;

	public function init()
	{
	}

	public function display()
	{
		$message_display = new SwatMessageDisplay();
		$message_display->title = 'Not Found';

		if ($this->message !== null)
			$message_display->add($this->message);
			
		$message_display->display();
	}

	public function process()
	{
	}

	public function setMessage($msg)
	{
		$this->message = $msg;
	}
}

?>
