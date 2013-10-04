<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/AdminUI.php';

/**
 * Administrator Not Found page
 *
 * @package   Admin
 * @copyright 2004 silverorange
 */
class AdminAdminSiteFront extends AdminPage
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(__DIR__.'/front.xml');
		$this->navbar->popEntry();
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		$note = $this->ui->getWidget('note');
		$note->title = sprintf(
			Admin::_('Welcome to the %s Admin!'),
			$this->app->config->site->title
		);
		$this->buildMessages();
	}

	// }}}
}

?>
