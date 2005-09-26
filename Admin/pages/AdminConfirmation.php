<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/AdminUI.php';

/**
 * Generic admin confirmation page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * confirmation page, inherit directly from AdminPage instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminConfirmation extends AdminPage
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->ui->loadFromXML(dirname(__FILE__).'/confirmation.xml');
		$this->navbar->createEntry(Admin::_('Confirmation'));
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();
		$form = $this->ui->getWidget('confirmation_form');

		if (!$form->isProcessed())
			return;

		$this->processResponse();
		$this->app->relocate($this->app->history->getHistory(0));
	}

	// }}}
	// {{{ protected function processResponse()

	/**
	 * Process the response
	 *
	 * This method is called to perform whatever processing is required in 
	 * response to the button clicked.
	 * Called by {@link AdminConfirmation::process}.
	 * Sub-classes should implement this method.
	 */
	abstract protected function processResponse();

	// }}}

	// build phase
	// {{{ protected function initDisplay()

	protected function initDisplay()
	{
		parent::initDisplay();
		$form = $this->ui->getWidget('confirmation_form');
		$form->action = $this->source;
	}
	
	// }}}
	// {{{ protected function switchToCancelButton()

	/**
	 * Switch to a cancel button.
	 * 
	 * Transforms the default Yes/No buttons in confirmation.xml into a cancel button.
	 */
	protected function switchToCancelButton()
	{
		$this->ui->getWidget('yes_button')->visible = false;
		$this->ui->getWidget('no_button')->title = Admin::_('Cancel');
	}

	// }}}
}

?>
