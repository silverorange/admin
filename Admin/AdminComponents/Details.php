<?php

require_once('Admin/AdminUI.php');
require_once('Admin/Admin/Index.php');
require_once('Admin/AdminTableStore.php');
require_once('SwatDB/SwatDB.php');

/**
 * Details page for AdminComponents
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsDetails extends AdminIndex {

	private $id;

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminComponents/details.xml');

		$this->id = intval(SwatApplication::initVar('id'));
	}

	public function display() {
		$fields = array('title'); 
		$row = SwatDB::queryRow($this->app->db, 'admincomponents', $fields, 'componentid', $this->id);

		$frame = $this->ui->getWidget('frame');
		$frame->title = $row->title;

		parent::display();
	}

	protected function getTableStore() {

		$sql = 'select adminsubcomponents.subcomponentid, 
					adminsubcomponents.title, 
					adminsubcomponents.shortname, 
					adminsubcomponents.show
				from adminsubcomponents 
				where component = %s
				order by adminsubcomponents.displayorder';

		$sql = sprintf($sql, $this->app->db->quote($this->id, 'integer'));

		$types = array('integer', 'text', 'text', 'integer', 'boolean', 'text');
		$store = $this->app->db->query($sql, $types, true, 'AdminTableStore');

		return $store;
	}

	protected function processActions() {
		$view = $this->ui->getWidget('view');
		$actions = $this->ui->getWidget('actions');

		switch ($actions->selected->name) {
			case 'delete':
				$this->app->replacePage('AdminComponents/Delete');
				$this->app->page->items = $view->checked_items;
				break;

			case 'show':
				SwatDB::updateColumn($this->app->db, 'admincomponents', 
					'boolean:show', true, 'componentid', 
					$view->checked_items);
				break;

			case 'hide':
				SwatDB::updateColumn($this->app->db, 'admincomponents', 
					'boolean:show', false, 'componentid', 
					$view->checked_items);
				break;

			case 'changesection':
				$new_section = $actions->selected->widget->value;

				SwatDB::updateColumn($this->app->db, 'admincomponents', 
					'integer:section', $new_section, 'componentid', 
					$view->checked_items);

				break;
		}
	}
}

?>
