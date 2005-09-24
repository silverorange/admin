<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/AdminUI.php';

/**
 * Administrator Not Found page
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminFront extends AdminPage
{
	public function initDisplay()
	{
		$this->initMessages();
	}


	public function process()
	{
	}

	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/front.xml');

		$this->navbar = new SwatNavBar();
		$this->navbar->createEntry('<h1>'.$this->app->title.'</h1>');
	}
}

?>
