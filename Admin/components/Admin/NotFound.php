<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Swat/SwatMessageDisplay.php';

/**
 * Administrator Not Found page
 *
 * @package   Admin
 * @copyright 2004 silverorange
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

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$message_display = new SwatMessageDisplay();

		if ($this->message !== null)
			$message_display->add($this->message);
		else
			$message_display->add(Admin::_('Not Found'));

		$this->ui->getRoot()->add($message_display);
	}

	// }}}
}

?>
