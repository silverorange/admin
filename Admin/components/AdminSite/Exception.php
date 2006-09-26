<?php

require_once 'Site/pages/SiteExceptionPage.php';
require_once 'Swat/SwatMessage.php';
require_once 'Swat/SwatMessageDisplay.php';

/**
 * Exception page in an admin application
 *
 * @package   Admin
 * @copyright 2006 silverorange
 */
class AdminAdminSiteException extends SiteExceptionPage
{
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new AdminLayout($this->app, 'Admin/layouts/xhtml/default.php');
	}

	// }}}

	// build phase
	// {{{ protected function display()

	protected function display($status)
	{
		printf('<p>%s</p>', $this->getSummary($status));

		echo '<p>This error has been reported.</p>';

		if ($this->exception !== null)
			$this->exception->process(false);
	}

	// }}}
}

?>
