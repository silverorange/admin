<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Swat/SwatString.php';

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
			($id === null) ? Admin::_('Add') : Admin::_('Edit'));
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
	// {{{ protected function generateShortname()

	/**
	 * Generate a shortname
	 *
	 * This method allows edit pages to easily generate a unique shortname by 
	 * calling this method during their processing phase. The shortname is 
	 * generated from the text provided using SwatString::condenseToName() and
	 * validated with AdminEdit::validateShortname().  If the initial shortname
	 * is not valid an integer is appended and incremented until the shortname
	 * is valid.  Sub-classes should override validateShortname() to perform
	 * whatever checks are necessary to validate the shortname.
	 *
	 * @param string $text Text to generate the shortname from.
	 * @param integer $id An identifier of the data object being edited.
	 * @return string A shortname.
	 */
	protected function generateShortname($text, $id)
	{
		$shortname_base = SwatString::condenseToName($text);
		$count = 1;
		$shortname = $shortname_base;

		while ($this->validateShortname($shortname, $id) === false)
			$shortname = $shortname_base.$count++;

		return $shortname;
	}

	// }}}
	// {{{ protected function validateShortname()

	/**
	 * Validate a shortname
	 *
	 * This method is called by AdminEdit::generateShortname().
	 * Sub-classes should override this method to perform
	 * whatever checks are necessary to validate the shortname.
	 *
	 * @param string $shortname The shortname to validate.
	 * @param integer $id An identifier of the data object being edited.
	 * @return boolean Whether the shortname is valid.
	 */
	protected function validateShortname($shortname, $id)
	{
		return true;
	}

	// }}}
	// {{{ protected function relocate()

	/**
	 * Relocate after process
	 */
	protected function relocate()
	{
		$form = $this->ui->getWidget('edit_form');
		$url = $form->getHiddenField(self::RELOCATE_URL_FIELD);
		$this->app->relocate($url);
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();
		$id = SwatApplication::initVar('id');

		if (is_numeric($id))
			$id = intval($id);

		$this->buildForm($id);
		$this->buildFrame($id);
		$this->buildButton($id);
		$this->buildMessages();
	}

	// }}}
	// {{{ protected function buildForm()

	protected function buildForm($id)
	{
		$form = $this->ui->getWidget('edit_form');

		if ($id !== null)
			if (!$form->isProcessed())
				$this->loadData($id);

		$form->action = $this->source;
		$form->addHiddenField('id', $id);

		if ($form->getHiddenField(self::RELOCATE_URL_FIELD) === null) {
			$url = $this->getRefererURL();
			$form->addHiddenField(self::RELOCATE_URL_FIELD, $url);
		}
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
	// {{{ protected function buildButton()

	protected function buildButton($id)
	{
		$button = $this->ui->getWidget('submit_button');

		if ($id === null)
			$button->setFromStock('create');
		else
			$button->setFromStock('apply');
	}

	// }}}
	// {{{ protected function buildFrame()

	protected function buildFrame($id)
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
