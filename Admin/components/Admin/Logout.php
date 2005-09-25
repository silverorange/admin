<?php

require_once 'Admin/pages/AdminPage.php';

/**
 * Very simple administrator logout page
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminLogout extends AdminPage
{
	// process phase
	// {{{ protected functipn processInternal()

	protected function processInternal()
	{
		$this->app->session->logout();
		$this->app->relocate($this->app->getBaseHref());
	}

	// }}}
}

?>
