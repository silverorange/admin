<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'MDB2.php';

/**
 * Edit page for AdminSections
 *
 * @package   Admin
 * @copyright 2004-2006 silverorange
 */
class AdminSectionsEdit extends AdminDBEdit
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML(dirname(__FILE__).'/edit.xml');

		$this->fields = array('title', 'boolean:show', 'description');
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array('title', 'show', 'description'));

		if ($this->id === null)
			$this->id = SwatDB::insertRow($this->app->db, 'adminsections',
				$this->fields, $values, 'integer:id');
		else
			SwatDB::updateRow($this->app->db, 'adminsections', $this->fields,
				$values, 'integer:id', $this->id);

		$msg = new SwatMessage(
			sprintf(Admin::_('Section “%s” has been saved.'),
			$values['title']), SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$row = SwatDB::queryRowFromTable($this->app->db, 'adminsections', 
			$this->fields, 'integer:id', $this->id);

		if ($row === null)
			throw new AdminNotFoundException(
				sprintf(Admin::_("Section with id '%s' not found."),
				$this->id));

		$this->ui->setValues(get_object_vars($row));
	}

	// }}}
}

?>
