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

	protected $ui;

	public function display() {
		$id = SwatApplication::initVar('id');
		$form = $this->ui->getWidget('edit_form');

		if ($id !== null)
			if (!$form->hasBeenProcessed())
				$this->loadData($id);

		$this->displayFrame($id);
		$this->displayButton($id);
		$this->displayMessages();

		$form->action = $this->source;

		if ($id !== null)
			$form->addHiddenField('id', $id);

		$root = $this->ui->getRoot();
		$root->display();
	}

	protected function displayButton($id) {
		$button = $this->ui->getWidget('submit_button');

		if ($id === null)
			$button->setTitleFromStock('create');
		else
			$button->setTitleFromStock('apply');
	}

	protected function displayFrame($id) {
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
			if (!$form->hasMessage()) {
				if ($this->saveData($id)) {
					$this->app->relocate($this->app->getHistory());
				}
			}
		}
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
