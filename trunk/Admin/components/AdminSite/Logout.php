<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';

/**
 * Very simple administrator logout page
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 */
class AdminSiteLogout extends AdminPage
{
	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		$this->layout->logout_form->process();

		if ($this->layout->logout_form->isProcessed()) {
			$this->app->session->logout();
			$this->app->relocate($this->app->getBaseHref());
		} else {
			throw new AdminNotFoundException();
		}
	}

	// }}}
}

?>
