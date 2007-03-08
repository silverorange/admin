<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/AdminUI.php';

/**
 * Generic admin confirmation page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * confirmation page, inherit directly from AdminPage instead.
 *
 * @package   Admin
 * @copyright 2004-2007 silverorange
 */
abstract class AdminConfirmation extends AdminPage
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->ui->loadFromXML(dirname(__FILE__).'/confirmation.xml');
		$this->navbar->createEntry(Admin::_('Confirmation'));
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();
		$form = $this->ui->getWidget('confirmation_form');

		if (!$form->isProcessed())
			return;

		$this->processResponse();
		$this->relocate();
	}

	// }}}
	// {{{ protected function processResponse()

	/**
	 * Process the response
	 *
	 * This method is called to perform whatever processing is required in 
	 * response to the button clicked. It is called by the
	 * {@link AdminConfirmation::process} method.
	 */
	abstract protected function processResponse();

	// }}}
	// {{{ protected function relocate()

	/**
	 * Relocates to the previous page after processsing confirmation response
	 */
	protected function relocate()
	{
		$form = $this->ui->getWidget('confirmation_form');
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
	}
	
	// }}}
	// {{{ protected function buildForm()

	protected function buildForm()
	{
		$form = $this->ui->getWidget('confirmation_form');
		$form->action = $this->source;

		if ($form->getHiddenField(self::RELOCATE_URL_FIELD) === null) {
			$url = $this->getRefererURL();
			$form->addHiddenField(self::RELOCATE_URL_FIELD, $url);
		}
	}

	// }}}
	// {{{ protected function switchToCancelButton()

	/**
	 * Switches the default yes/no buttons to a cancel button
	 *
	 * Call this method if a confirmation page is displayed and the desired
	 * action of the user is impossible.
	 */
	protected function switchToCancelButton()
	{
		$this->ui->getWidget('yes_button')->visible = false;
		$this->ui->getWidget('no_button')->title = Admin::_('Cancel');
	}

	// }}}
}

?>
