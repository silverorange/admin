<?php

require_once 'Admin/AdminPage.php';
require_once 'Admin/AdminUI.php';

/**
 * Administrator Not Found page
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminFront extends AdminPage
{
	public function init()
	{
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/Admin/front.xml');

		$this->navbar = new SwatNavBar();
		$this->navbar->createEntry('<h1>'.$this->app->title.'</h1>');
	}

	public function initDisplay()
	{
		$this->initMessages();
	}


	public function process()
	{
	}
}

?>
