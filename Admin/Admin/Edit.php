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
		$btn_submit = $this->ui->getWidget('btn_submit');
		$frame = $this->ui->getWidget('frame');

		if ($id == 0) {
			$btn_submit->setTitleFromStock('create');
			$frame->title = 'New '.$frame->title;
		} else {
			$this->loadData($id);
			$btn_submit->setTitleFromStock('apply');
			$frame->title .= ' Edit';
		}

		$form = $this->ui->getWidget('editform');
		$form->action = $this->source;
		$form->addHiddenField('id', $id);

		$root = $this->ui->getRoot();
		$root->display();
	}

	public function process() {
		$form = $this->ui->getWidget('editform');
		$id = intval(SwatApplication::initVar('id'));

		if ($form->process()) {
			if (!$form->hasErrorMessage()) {
				$this->saveData($id);
				$this->app->relocate($this->component);
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
