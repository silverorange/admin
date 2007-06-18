<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'MDB2.php';
require_once 'Admin/dataobjects/AdminSection.php';

/**
 * Edit page for AdminSections
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 */
class AdminAdminSectionEdit extends AdminDBEdit
{
	// private properties

	private $section;

	// }}}
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->initSection();

		$this->ui->loadFromXML(dirname(__FILE__).'/edit.xml');

		$this->fields = array('title', 'boolean:show', 'description');
	}

	// }}}
	// {{{ protected function initSection()
	protected function initSection()
	{
		$this->section = new AdminSection();
		$this->section->setDatabase($this->app->db);

		if (!$this->section->load($this->id))
			throw new AdminNotFoundException(
				sprintf(Admin::_('Section with id "%s" notfound.'),
						$this->id));
		}
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array(
			'title', 
			'show', 
			'description'));

		$this->section->title = $values['title'];
		$this->section->show = $values['show'];
		$this->section->description = $values['description'];
		$this->section->save();

		$message = new SwatMessage(
			sprintf(Admin::_('Section “%s” has been saved.'),
			$values['title']), SwatMessage::NOTIFICATION);

		$this->app->messages->add($message);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->section));
	}

	// }}}
}

?>
