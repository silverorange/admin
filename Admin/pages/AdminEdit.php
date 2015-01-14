<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Swat/SwatString.php';

/**
 * Generic admin edit page
 *
 * This class is intended to be a convenience base class. For a fully custom
 * edit page, inherit directly from AdminPage instead.
 *
 * @package   Admin
 * @copyright 2004-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminEdit extends AdminPage
{
	// {{{ protected variables

	protected $id;

	// }}}
	// {{{ protected function isNew()

	protected function isNew()
	{
		return ($this->id === null);
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->id = SiteApplication::initVar('id');

		if (is_numeric($this->id)) {
			$this->id = intval($this->id);
		}
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		$form = $this->ui->getWidget('edit_form');

		if ($form->isProcessed()) {
			$validated = $this->validate();

			// validate() doesn't necessarily return true/false, often it
			// will return null, so explicitly check false here
			if ($validated === false || $form->hasMessage()) {
				$message = new SwatMessage(
					Admin::_(
						'There is a problem with the information submitted.'
					),
					'error'
				);

				$message->secondary_content = Admin::_(
					'Please address the fields highlighted below and '.
					're-submit the form.'
				);

				$this->app->messages->add($message);
			} else {
				if ($this->saveData()) {
					$this->relocate();
				}
			}
		}
	}

	// }}}
	// {{{ protected function validate()

	/**
	 * Sub-classes should implement this method to perform validation.
	 *
	 * @return boolean true if validation was successful.
	 */
	protected function validate()
	{
	}

	// }}}
	// {{{ abstract protected function saveData()

	/**
	 * Save the data
	 *
	 * This method is called to save data from the widgets after processing.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to store the data. Widgets can be accessed through the
	 * $ui class variable.
	 *
	 * @return boolean true if save was successful.
	 */
	abstract protected function saveData();

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
	 * @return string A shortname.
	 */
	protected function generateShortname($text)
	{
		$shortname_base = SwatString::condenseToName($text);
		$count = 1;
		$shortname = $shortname_base;

		while ($this->validateShortname($shortname) === false) {
			$shortname = $shortname_base.$count++;
		}

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
	 * @return boolean Whether the shortname is valid.
	 */
	protected function validateShortname($shortname)
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

		$this->buildForm();
		$this->buildFrame();
		$this->buildButton();
		$this->buildMessages();
	}

	// }}}
	// {{{ protected function buildForm()

	protected function buildForm()
	{
		$form_found = true;
		try {
			$form = $this->ui->getWidget('edit_form');
		} catch (SwatWidgetNotFoundException $e) {
			$form_found = false;
		}

		if ($form_found) {
			if (!$this->isNew() && !$form->isProcessed()) {
				$this->loadData();
			}

			$form->action = $this->source;
			$form->autofocus = true;
			$form->addHiddenField('id', $this->id);

			if ($form->getHiddenField(self::RELOCATE_URL_FIELD) === null) {
				$url = $this->getRefererURL();
				$form->addHiddenField(self::RELOCATE_URL_FIELD, $url);
			}
		}
	}

	// }}}
	// {{{ abstract protected function loadData()

	/**
	 * Load the data
	 *
	 * This method is called to load data to be edited into the widgets.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to obtain the data. Widgets can be accessed through the
	 * $ui class variable.
	 *
	 * @return boolean true if load was successful.
	 */
	abstract protected function loadData();

	// }}}
	// {{{ protected function buildButton()

	protected function buildButton()
	{
		$button = $this->ui->getWidget('submit_button');

		if ($button->title == 'Submit') {
			if ($this->isNew()) {
				$button->setFromStock('create');
			} else {
				$button->setFromStock('apply');
			}
		}
	}

	// }}}
	// {{{ protected function buildFrame()

	protected function buildFrame()
	{
		$frame = $this->ui->getWidget('edit_frame');

		if ($this->isNew()) {
			$frame->title = sprintf(
				Admin::_('New %s'),
				$frame->title
			);
		} else {
			$frame->title = sprintf(
				Admin::_('Edit %s'),
				$frame->title
			);
		}
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		if ($this->isNew()) {
			$title = Admin::_('New');
		} else {
			$title = Admin::_('Edit');
		}

		$this->navbar->createEntry($title);
	}

	// }}}
}

?>
