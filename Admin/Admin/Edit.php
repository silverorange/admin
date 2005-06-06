<?php

require_once("Admin/AdminPage.php");

/**
 * Generic admin edit page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * edit page, inherit directly from AdminPage instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminEdit extends AdminPage {

	public function displayInit() {
		$id = SwatApplication::initVar('id');
		$form = $this->ui->getWidget('edit_form');

		if ($id !== null)
			if (!$form->hasBeenProcessed())
				$this->loadData($id);

		$this->displayInitFrame($id);
		$this->displayInitButton($id);
		$this->displayInitMessages();

		$form->action = $this->source;
		$form->addHiddenField('id', $id);
	}

	protected function displayInitButton($id) {
		$button = $this->ui->getWidget('submit_button');

		if ($id === null)
			$button->setTitleFromStock('create');
		else
			$button->setTitleFromStock('apply');
	}

	protected function displayInitFrame($id) {
		$frame = $this->ui->getWidget('edit_frame');

		if ($id === null)
			$frame->title = sprintf(_S("New %s"), $frame->title);
		else
			$frame->title = sprintf(_S("Edit %s"), $frame->title);
	}

	public function process() {
		$form = $this->ui->getWidget('edit_form');
		$id = SwatApplication::initVar('id');

		if ($form->process()) {
			$this->processPage();

			if (!$form->hasMessage()) {
				if ($this->saveData($id)) {
					$this->relocate();
				}
			}
		}
	}

	/**
	 * Additional page-level processing
	 */
	protected function processPage() {

	}

	/**
	 * Relocate after process
	 */
	protected function relocate() {
		$this->app->relocate($this->app->getHistory());
	}

	/**
	 * Save the data
	 *
	 * This method is called to save data from the widgets after processing.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to store the data. Widgets can be accessed through the
	 * $ui class variable.
	 *
	 * @param integer $id An integer identifier of the data to store.
	 * @return boolean True if save was successful.
	 */
	abstract protected function saveData($id);

	/**
	 * Load the data
	 *
	 * This method is called to load data to be edited into the widgets.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to obtain the data. Widgets can be accessed through the
	 * $ui class variable.
	 *
	 * @param integer $id An integer identifier of the data to retrieve.
	 * @return boolean True if load was successful.
	 */
	abstract protected function loadData($id);
}

?>
