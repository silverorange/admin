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
	public function initDisplay()
	{
		$this->initMessages();
	}


	public function process()
	{
	}

	protected function initInternal()
	{
		$this->ui->loadFromXML('Admin/Admin/front.xml');

		$this->navbar = new SwatNavBar();
		$this->navbar->createEntry('<h1>'.$this->app->title.'</h1>');
	}
}

?>
