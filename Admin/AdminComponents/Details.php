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
		assert($this->id !== null);

		$form = $this->ui->getWidget('index_form');
		$form->addHiddenField('id', $this->id);
	}

	public function display() {
		$fields = array('title'); 
		$row = SwatDB::queryRow($this->app->db, 'admincomponents', $fields, 'componentid', $this->id);

		$frame = $this->ui->getWidget('index_frame');
		$frame->title = $row->title;

		$sub_frame = $this->ui->getWidget('sub_frame');

		foreach ($sub_frame->getChildren('SwatToolLink') as $tool)
			$tool->value = $this->id;

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

		$types = array('integer', 'text', 'text', 'boolean');
		$store = $this->app->db->query($sql, $types, true, 'AdminTableStore');

		return $store;
	}

	protected function processActions() {
		$view = $this->ui->getWidget('index_view');
		$actions = $this->ui->getWidget('index_actions');

		switch ($actions->selected->name) {
			case 'delete':
				$this->app->replacePage('AdminSubComponents/Delete');
				$this->app->page->items = $view->checked_items;
				break;

			case 'show':
				SwatDB::updateColumn($this->app->db, 'adminsubcomponents', 
					'boolean:show', true, 'subcomponentid', 
					$view->checked_items);
				break;

			case 'hide':
				SwatDB::updateColumn($this->app->db, 'adminsubcomponents', 
					'boolean:show', false, 'subcomponentid', 
					$view->checked_items);
				break;

		}
	}
}

?>
