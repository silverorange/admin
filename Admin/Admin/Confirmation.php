<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Admin/AdminPage.php');

/**
 * Base class for a standard admin confirmation page.
 * This class is intended to be a convenience class. For a fully custom page
 * inherit from AdminPage directly instead.
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
	 * Display the message.
	 * This method is called to display the message body of the confirmation 
	 * page. Sub-classes should implement this method and do whatever is 
	 * necessary to generate the confirmation message.
	 */
	abstract protected function displayMessage();

	public function process() {
		$form = $this->ui->getWidget('confirmform');

		if (!$form->process())
			return;

		$this->processConfirmation();

		$this->app->relocate($this->component);
	}

	/**
	 * Process the response.
	 * This method is called to perform whatever processing is required in 
	 * response to the button clicked. Sub-classes should implement this method.
	 */
	abstract protected function processResponse();
}

?>
