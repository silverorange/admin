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

		$section_flydown = $this->ui->getWidget('section');
		$section_flydown->options = SwatDB::getOptionArray($this->app->db, 
			'adminsections', 'title', 'sectionid', 'displayorder');
	}

	protected function getTableStore() {
		$sql = 'select admincomponents.componentid,
					admincomponents.title, 
					admincomponents.shortname, 
					admincomponents.section, 
					admincomponents.show,
					admincomponents.enabled,
					adminsections.title as section_title
				from admincomponents 
				inner join adminsections 
					on adminsections.sectionid = admincomponents.section
				order by adminsections.displayorder, adminsections.sectionid, %s';

		$sql = sprintf($sql,
			$this->getOrderByClause('admincomponents.displayorder, admincomponents.title', 'admincomponents'));

		$types = array('integer', 'text', 'text', 'integer', 'boolean', 'text');
		$store = $this->app->db->query($sql, $types, true, 'AdminTableStore');

		return $store;
	}

	protected function processActions() {
		$view = $this->ui->getWidget('index_view');
		$actions = $this->ui->getWidget('index_actions');
		$num = count($view->checked_items);

		switch ($actions->selected->name) {
			case 'delete':
				$this->app->replacePage('AdminComponents/Delete');
				$this->app->page->items = $view->checked_items;
				break;

			case 'show':
				SwatDB::updateColumn($this->app->db, 'admincomponents', 
					'boolean:show', true, 'componentid', 
					$view->checked_items);

				$this->app->addMessage(sprintf(_nS('%d component has been shown.', 
					'%d components have been shown.', $num), $num));

				break;

			case 'hide':
				SwatDB::updateColumn($this->app->db, 'admincomponents', 
					'boolean:show', false, 'componentid', 
					$view->checked_items);

				$this->app->addMessage(sprintf(_nS('%d component has been hidden.', 
					'%d components have been hidden.', $num), $num));

				break;

			case 'enable':
				SwatDB::updateColumn($this->app->db, 'admincomponents', 
					'boolean:enabled', true, 'componentid', 
					$view->checked_items);

				$this->app->addMessage(sprintf(_nS('%d component has been enabled.', 
					'%d components have been enabled.', $num), $num));

				break;

			case 'disable':
				SwatDB::updateColumn($this->app->db, 'admincomponents', 
					'boolean:enabled', false, 'componentid', 
					$view->checked_items);

				$this->app->addMessage(sprintf(_nS('%d component has been disabled.', 
					'%d components have been disabled.', $num), $num));

				break;

			case 'change_section':
				$new_section = $actions->selected->widget->value;

				SwatDB::updateColumn($this->app->db, 'admincomponents', 
					'integer:section', $new_section, 'componentid', 
					$view->checked_items);

				$title = SwatDB::queryOne($this->app->db, 'adminsections', 'text:title',
					'sectionid', $new_section);

				$this->app->addMessage(sprintf(_nS('%d component has been moved to section "%s".', 
					'%d components have been moved to section "%s".', $num), $num, $title));

				break;
		}
	}
}

?>
