<?php

require_once('Admin/AdminPage.php');

/**
 * Generic admin confirmation page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * confirmation page, inherit directly from AdminPage instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminConfirmation extends AdminPage {

	protected $ui;

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/confirmation.xml');
	}

	public function display() {
		$form = $this->ui->getWidget('confirmform');
		$form->action = $this->source;

		$this->displayMessage();

		$root = $this->ui->getRoot();
		$root->displayTidy();
	}

	/**
	 * Display the message
	 *
	 * This method is called to display the message body of the confirmation 
	 * page. Sub-classes should implement this method and do whatever is 
	 * necessary to generate the confirmation message.
	 */
	abstract protected function displayMessage();

	public function process() {
		$form = $this->ui->getWidget('confirmform');

		if (!$form->process())
			return;

		$this->processResponse();

		$this->app->relocate($this->component);
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
