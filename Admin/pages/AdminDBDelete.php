<?php

require_once 'Admin/pages/AdminDBConfirmation.php';
require_once 'SwatDB/SwatDBException.php';

/**
 * Generic admin database delete page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * delete page, inherit directly from {@link AdminConfirmation} or
 * {@link @AdminDBConfirmation} instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminDBDelete extends AdminDBConfirmation
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->single_delete = (boolean)SwatApplication::initVar('single_delete', false, SwatApplication::VAR_POST);

		$id = SwatApplication::initVar('id', null, SwatApplication::VAR_GET);

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

		$this->navbar->popEntry(1);
		$this->navbar->createEntry(Admin::_('Delete'));
	}

	// }}}

	// process phase
	// {{{ protected function generateMessage()

	protected function generateMessage(Exception $e)
	{
		if ($e instanceof SwatDBException) {
			$msg = new SwatMessage(Admin::_('A database error has occured.
				The item(s) were not deleted.'),
				 SwatMessage::SYSTEM_ERROR);
		} else {
			$msg = new SwatMessage(Admin::_('An error has occured.
				The item(s) were not deleted.'),
				SwatMessage::SYSTEM_ERROR);
		}

		$this->app->messages->add($msg);	
	}

	// }}}
}

?>
