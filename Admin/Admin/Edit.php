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
		$id = intval(SwatApplication::initVar('id'));
		$button = $this->ui->getWidget('submit_button');
		$frame = $this->ui->getWidget('edit_frame');
		$form = $this->ui->getWidget('edit_form');

		if ($id == 0) {
			$button->setTitleFromStock('create');
			$frame->title = 'New '.$frame->title;
		} else {
			if (!$form->processed)
				$this->loadData($id);

			$button->setTitleFromStock('apply');
			$frame->title .= ' Edit';
		}

		$form->action = $this->source;
		$form->addHiddenField('id', $id);

		$root = $this->ui->getRoot();
		$root->display();
	}

	public function process() {
		$form = $this->ui->getWidget('edit_form');
		$id = intval(SwatApplication::initVar('id'));

		if ($form->process()) {
			if (!$form->hasErrorMessage()) {
				$this->saveData($id);
				$this->app->relocate($this->app->getHistory());
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
	 */
	abstract protected function loadData($id);
}
?>
