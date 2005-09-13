<?php

require_once 'Admin/AdminPage.php';
require_once 'Swat/SwatMessageDisplay.php';

/**
 * Administrator Not Access page
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminNoAccess extends AdminPage
{
	private $message = null;

	protected function initInternal()
	{
		$this->app->getPage()->navbar->replaceElement(1, Admin::_('No Access'));
	}

	public function display()
	{
		$message_display = new SwatMessageDisplay();
		$message_display->title = 'No Access';

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
