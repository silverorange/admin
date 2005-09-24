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
	private $message = null;

	public function init()
	{
	}

	public function build()
	{
		$this->initDisplay();

		ob_start();
		$html_head_entry = new SwatHtmlHeadEntry('swat/styles/swat.css',
			SwatHtmlHeadEntry::TYPE_STYLE);

		$html_head_entry->display();
		$this->layout->html_head_entries = ob_get_clean();

		$this->layout->title = $this->app->title.' | '.$this->title;
		$this->layout->basehref = $this->app->getBaseHref();

		ob_start();
		$this->displayHeader();
		$this->layout->header = ob_get_clean();

		ob_start();
		$this->navbar->display();	
		$this->layout->navbar = ob_get_clean();

		ob_start();
		$this->displayMenu();
		$this->layout->menu = ob_get_clean();

		ob_start();
		$this->display();
		$this->layout->content = ob_get_clean();
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
