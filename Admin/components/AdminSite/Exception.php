<?php

require_once 'Admin/layouts/AdminDefaultLayout.php';
require_once 'Site/pages/SiteXhtmlExceptionPage.php';
require_once 'Swat/SwatMessage.php';
require_once 'Swat/SwatMessageDisplay.php';

/**
 * Exception page in an admin application
 *
 * @package   Admin
 * @copyright 2006-2015 silverorange
 */
class AdminAdminSiteException extends SiteXhtmlExceptionPage
{
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new AdminDefaultLayout($this->app,
			'Admin/layouts/xhtml/default.php');
	}

	// }}}

	// build phase
	// {{{ public function build()

	public function build()
	{
		parent::build();
		if (isset($this->layout->navbar)) {
			$this->layout->navbar->popEntry();
			$this->layout->navbar->popEntry();
			$this->layout->navbar->createEntry('Error');
		}
	}

	// }}}
	// {{{ protected function display()

	protected function display()
	{
		printf('<p>%s</p>', $this->getSummary());

		echo '<p>This error has been reported.</p>';

		if ($this->exception !== null)
			$this->exception->process(false);
	}

	// }}}
}

?>
