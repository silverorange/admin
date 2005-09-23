<?php

require_once 'Admin/AdminPage.php';
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
	public function initInternal()
	{
		$this->ui->loadFromXML('Admin/Admin/confirmation.xml');

		$this->navbar->createEntry(Admin::_('Confirmation'));
	}

	/**
	 * Display the page
	 *
	 * Sub-classes should override this method to do whatever is necessary 
	 * to generate the confirmation message and then call parent::initDisplay().
	 */
	public function initDisplay()
	{
		$form = $this->ui->getWidget('confirmation_form');
		$form->action = $this->source;
	}
	
	public function process()
	{
		$this->ui->process();
		$form = $this->ui->getWidget('confirmation_form');

		if (!$form->isProcessed())
			return;

		$this->processResponse();

		$this->app->relocate($this->app->history->getHistory(0));
	}

	/**
	 * Switch to a cancel button.
	 *
	 * Transforms the default Yes/No buttons in confirmation.xml into a cancel button.
	 */
	protected function displayCancelButton()
	{
		$this->ui->getWidget('yes_button')->visible = false;
		$this->ui->getWidget('no_button')->title = Admin::_('Cancel');
	}

	/**
	 * Process the response
	 *
	 * This method is called to perform whatever processing is required in 
	 * response to the button clicked.
	 * Called by {@link AdminConfirmation::process}.
	 * Sub-classes should implement this method.
	 */
	abstract protected function processResponse();
}

?>
