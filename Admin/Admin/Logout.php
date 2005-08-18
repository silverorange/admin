<?php

require_once 'Admin/AdminPage.php';

/**
 * Very simple administrator logout page
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminLogout extends AdminPage
{
	public function init()
	{
	}

	public function process()
	{
		$this->app->logout();
		$this->app->relocate($this->app->getBaseHref());
	}
}

?>
