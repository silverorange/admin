<?php

require_once 'Admin/AdminPage.php';

/**
 * Generic admin edit page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * edit page, inherit directly from AdminPage instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminEdit extends AdminPage
{
	public function initDisplay()
	{
		$id = SwatApplication::initVar('id');
		$form = $this->ui->getWidget('edit_form');

		if ($id !== null)
			if (!$form->hasBeenProcessed())
				$this->loadData($id);

		$this->initFrame($id);
		$this->initButton($id);
		$this->initMessages();

		$form->action = $this->source;
		$form->addHiddenField('id', $id);
	}

	protected function initButton($id)
	{
		$button = $this->ui->getWidget('submit_button');

		if ($id === null)
			$button->setTitleFromStock('create');
		else
			$button->setTitleFromStock('apply');
	}

	protected function initFrame($id)
	{
		$frame = $this->ui->getWidget('edit_frame');

		if ($id === null)
			$frame->title = sprintf(Admin::_('New %s'), $frame->title);
		else
			$frame->title = sprintf(Admin::_('Edit %s'), $frame->title);
	}

	public function process()
	{
		$form = $this->ui->getWidget('edit_form');
		$id = SwatApplication::initVar('id');

		if ($form->process()) {
			$this->processPage($id);

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
	protected function processPage($id)
	{

	}

	/**
	 * Relocate after process
	 */
	protected function relocate()
	{
		$this->app->relocate($this->app->history->getHistory());
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
