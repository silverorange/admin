<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'MDB2.php';

/**
 * Edit page for AdminSections
 *
 * @package Admin
 * @copyright silverorange 2004
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

	protected function saveDBData($id)
	{
		$values = $this->ui->getValues(array('title', 'show', 'description'));

		if ($id === null)
			$id = SwatDB::insertRow($this->app->db, 'adminsections', $this->fields,
				$values, 'integer:id');
		else
			SwatDB::updateRow($this->app->db, 'adminsections', $this->fields,
				$values, 'integer:id', $id);

		$msg = new SwatMessage(
			sprintf(Admin::_('Section &#8220;%s&#8221; has been saved.'),
			$values['title']), SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData($id)
	{
		$row = SwatDB::queryRowFromTable($this->app->db, 'adminsections', 
			$this->fields, 'integer:id', $id);

		if ($row === null)
			return $this->app->replacePageNoAccess();

		$this->ui->setValues(get_object_vars($row));
	}

	// }}}
}

?>
