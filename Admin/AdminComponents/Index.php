<?php

require_once('Admin/AdminUI.php');
require_once('Admin/Admin/Index.php');
require_once('Admin/AdminTableStore.php');
require_once('SwatDB/SwatDB.php');

/**
 * Index page for AdminComponents
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsIndex extends AdminIndex {

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminComponents/index.xml');

		$sectionfly = $this->ui->getWidget('section');
		$sectionfly->options = SwatDB::getOptionArray($this->app->db, 
			'adminsections', 'title', 'sectionid', 'displayorder');
	}

	protected function getTableStore() {
		$sql = 'select admincomponents.componentid, 
					admincomponents.title, 
					admincomponents.shortname, 
					admincomponents.section, 
					admincomponents.hidden,
					adminsections.title as section_title
				from admincomponents 
				inner join adminsections 
					on adminsections.sectionid = admincomponents.section
				order by adminsections.displayorder, adminsections.sectionid, 
					admincomponents.displayorder';

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
				SwatDB::updateField($this->app->db, 'admincomponents', 
					'boolean:hidden', false, 'componentid', 
					$view->checked_items);

				break;

			case 'hide':
				SwatDB::updateField($this->app->db, 'admincomponents', 
					'boolean:hidden', true, 'componentid', 
					$view->checked_items);

				break;

			case 'changesection':
				$new_section = $actions->selected->widget->value;

				SwatDB::updateField($this->app->db, 'admincomponents', 
					'integer:section', $new_section, 'componentid', 
					$view->checked_items);

				break;
		}
	}
}

?>
