<?php

require_once 'Site/pages/SiteExceptionPage.php';
require_once 'Swat/SwatMessage.php';
require_once 'Swat/SwatMessageDisplay.php';

/**
 * Administrator Exception page
 *
 * @package Admin
 * @copyright silverorange 2006
 */
class AdminSiteException extends SiteExceptionPage
{
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new AdminLayout($this->app, 'Admin/layouts/xhtml/default.php');
	}

	// }}}
}

?>
