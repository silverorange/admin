<?php

require_once 'Admin/pages/AdminPage.php';

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
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$id = SwatApplication::initVar('id');
		$this->navbar->createEntry(
			($id == 0) ? Admin::_('Add') : Admin::_('Edit'));
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()


	protected function processInternal()
	{
		parent::processInternal();
		$form = $this->ui->getWidget('edit_form');
		$id = SwatApplication::initVar('id');

		if (is_numeric($id))
			$id = intval($id);

		if ($form->isProcessed()) {
			$this->validate($id);

			if ($form->hasMessage()) {
				$msg = new SwatMessage(Admin::_('REWRITE: There is a problem below.'), SwatMessage::ERROR);
				$this->app->messages->add($msg);
			} else {
				if ($this->saveData($id)) {
					$this->relocate();
				}
			}
		}
	}

	// }}}
	// {{{ protected function validate()

	/**
	 * Sub-classes should implement this method to preform validation.
	 */
	protected function validate($id)
	{
	}

	// }}}
	// {{{ protected function saveData()

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

	// }}}
	// {{{ protected function relocate()

	/**
	 * Relocate after process
	 */
	protected function relocate()
	{
		$this->app->relocate($this->app->history->getHistory());
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();
		$id = SwatApplication::initVar('id');
		$form = $this->ui->getWidget('edit_form');

		if (is_numeric($id))
			$id = intval($id);

		if ($id !== null)
			if (!$form->isProcessed())
				$this->loadData($id);

		$this->initFrame($id);
		$this->initButton($id);
		$this->initMessages();

		$form->action = $this->source;
		$form->addHiddenField('id', $id);
	}

	// }}}
	// {{{ protected function loadData()

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

	// }}}
	// {{{ protected function initButton()

	protected function initButton($id)
	{
		$button = $this->ui->getWidget('submit_button');

		if ($id === null)
			$button->setFromStock('create');
		else
			$button->setFromStock('apply');
	}

	// }}}
	// {{{ protected function initFrame()

	protected function initFrame($id)
	{
		$frame = $this->ui->getWidget('edit_frame');

		if ($id === null)
			$frame->title = sprintf(Admin::_('New %s'), $frame->title);
		else
			$frame->title = sprintf(Admin::_('Edit %s'), $frame->title);
	}

	// }}}
}

?>
