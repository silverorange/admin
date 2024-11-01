<?php

/**
 * Generic admin confirmation page
 *
 * This class is intended to be a convenience base class. For a fully custom
 * confirmation page, inherit directly from AdminPage instead.
 *
 * @package   Admin
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminConfirmation extends AdminPage
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->ui->loadFromXML($this->getUiXml());
		$this->navbar->createEntry(Admin::_('Confirmation'));
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return __DIR__.'/confirmation.xml';
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		$form = $this->ui->getWidget('confirmation_form');
		$relocate = false;

		if ($form->isAuthenticated()) {
			if ($form->isProcessed()) {
				if ($this->ui->getWidget('no_button')->hasBeenClicked()) {
					// if the no (aka cancel) button has been hit, relocate even
					// if the form doesn't validate or process.
					$relocate = true;
				} elseif (!$form->hasMessage()) {
					// only process the response if the form validated and we're
					// not already relocating.
					$this->processResponse();

					$relocate = true;
				}
			}

			if ($relocate) {
				$this->relocate();
			}
		} else {
			$message = new SwatMessage(Admin::_('There is a problem with the '.
				'information submitted.'), 'warning');

			$message->secondary_content =
				Admin::_('In order to ensure your security, we were unable to '.
				'process your request. Please try again.');

			$this->app->messages->add($message);
		}
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
	abstract protected function processResponse(): void;

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
		$this->buildMessages();
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
