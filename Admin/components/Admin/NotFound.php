<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Swat/SwatMessageDisplay.php';

/**
 * Administrator Not Found page
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminNotFound extends AdminPage
{
	// {{{ private properties

	private $message = null;

	// }}}
	// {{{ public function setMessage()

	public function setMessage($msg)
	{
		$this->message = $msg;
	}

	// }}}

	// build phase
	// {{{ protected function display()

	protected function display()
	{
		$message_display = new SwatMessageDisplay();
		$message_display->title = 'Not Found';

		if ($this->message !== null)
			$message_display->add($this->message);

		$message_display->display();
	}

	// }}}
}

?>
