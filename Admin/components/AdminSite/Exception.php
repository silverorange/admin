<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Swat/SwatMessage.php';
require_once 'Swat/SwatMessageDisplay.php';

/**
 * Administrator Exception page
 *
 * @package Admin
 * @copyright silverorange 2006
 */
class AdminSiteException extends AdminPage
{
	// {{{ private properties

	private $exception = null;

	// }}}
	// {{{ public function setException()

	public function setException($e)
	{
		$this->exception = $e;
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/exception.xml');
	}

	// }}}

	// build phase
	// {{{ public function buildInternal()

	public function buildInternal()
	{
		$message_display = $this->ui->getWidget('message_display');

		if ($this->exception !== null) {

			if ($this->exception->title === null)
				$this->title = get_class($this->exception);
			else
				$this->title = $this->exception->title;
	
			$message = new SwatMessage($this->title, SwatMessage::ERROR);
			$message->secondary_content = $this->exception->getMessage();
		}

		$this->navbar->replaceEntryByPosition(1,
			new SwatNavBarEntry($this->title));

		$message_display->add($message); 
	}

	// }}}
}

?>
