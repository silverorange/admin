<?php

require_once 'Admin/pages/AdminDBConfirmation.php';
require_once 'SwatDB/exceptions/SwatDBException.php';

/**
 * Generic admin database delete page
 *
 * This class is intended to be a convenience base class. For a fully custom
 * delete page, inherit directly from {@link AdminConfirmation} or
 * {@link AdminDBConfirmation} instead.
 *
 * @package   Admin
 * @copyright 2004-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminDBDelete extends AdminDBConfirmation
{
	// {{{ protected properties

	protected $single_delete = false;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->single_delete = (boolean)SiteApplication::initVar(
			'single_delete', false, SiteApplication::VAR_POST);

		$id = SiteApplication::initVar('id', null, SiteApplication::VAR_GET);

		if ($id !== null) {
			$this->setItems(array($id));
			$this->single_delete = true;
			$form = $this->ui->getWidget('confirmation_form');
			$form->addHiddenField('single_delete', $this->single_delete);
		}

		$yes_button = $this->ui->getWidget('yes_button');
		$yes_button->setFromStock('delete');

		$no_button = $this->ui->getWidget('no_button');
		$no_button->setFromStock('cancel');

		$this->navbar->popEntry();
		$this->navbar->createEntry(Admin::_('Delete'));
	}

	// }}}

	// process phase
	// {{{ protected function generateMessage()

	protected function generateMessage(Exception $e)
	{
		if ($e instanceof SwatDBException) {
			$message = new SwatMessage(Admin::_('A database error has occured.
				The item(s) were not deleted.'),
				 SwatMessage::SYSTEM_ERROR);
		} else {
			$message = new SwatMessage(Admin::_('An error has occured.
				The item(s) were not deleted.'),
				SwatMessage::SYSTEM_ERROR);
		}

		$this->app->messages->add($message);
	}

	// }}}
	// {{{ protected function relocate()

	protected function relocate()
	{
		$form = $this->ui->getWidget('confirmation_form');

		// prevent relocating to detail pages that no longer exist
		if ($form->button->id == 'no_button') {
			parent::relocate();
		} else {
			$this->app->relocate($this->getComponentName());
		}
	}

	// }}}
}

?>
