<?php

require_once 'Admin/Admin/DBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'MDB2.php';

/**
 * Edit page for AdminSections
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminSectionsEdit extends AdminDBEdit
{
	public function init()
	{
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminSections/edit.xml');
		
		$this->fields = array('title', 'boolean:show', 'description');
	}

	public function process()
	{
		$form = $this->ui->getWidget('edit_form');
		$id = intval(SwatApplication::initVar('id'));

		if ($form->process()) {
			if (!$form->hasMessage()) {
				$this->saveData($id);
				$this->app->relocate($this->app->history->getHistory());
			}
		}
	}

	protected function saveDBData($id)
	{
		$values = $this->ui->getValues(array('title', 'show', 'description'));

		if ($id == 0)
			$id = SwatDB::insertRow($this->app->db, 'adminsections', $this->fields,
				$values, 'integer:sectionid');
		else
			SwatDB::updateRow($this->app->db, 'adminsections', $this->fields,
				$values, 'integer:sectionid', $id);

		$msg = new SwatMessage(sprintf(Admin::_('Section "%s" has been saved.'), $values['title']), SwatMessage::INFO);
		$this->app->messages->add($msg);	
	}

	protected function loadDBData($id) {

		$row = SwatDB::queryRow($this->app->db, 'adminsections', 
			$this->fields, 'integer:sectionid', $id);

		if ($row === null)
			return $this->app->replacePageNoAccess();

		$this->ui->setValues(get_object_vars($row));
	}
}

?>
