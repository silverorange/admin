<?php

require_once('Admin/AdminUI.php');
require_once('Admin/AdminDB.php');
require_once("Admin/AdminPage.php");
require_once('Admin/AdminTableStore.php');

class AdminSectionsIndex extends AdminPage {

	private $ui;

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminSections/index.xml');

		$colorfly = $this->ui->getWidget('color');
		//$colorfly->options = 
		//	array(0 => _('red'), 1 => _('yellow'), 2 => _('blue'));
		$colorfly->options = AdminDB::getOptionArray($this->app->db, 
			'adminsections', 'title', 'text', 'sectionid');
	}

	public function display() {
		$view = $this->ui->getWidget('view');
		$view->model = $this->getTableStore();

		$form = $this->ui->getWidget('indexform');
		$form->action = $this->source;

		$root = $this->ui->getRoot();
		$root->display();
	}

	private function getTableStore() {
		$sql = 'SELECT sectionid, title, hidden 
				FROM adminsections 
				ORDER BY displayorder';

		$types = array('integer', 'text', 'boolean');
		$store = $this->app->db->query($sql, $types, true, 'AdminTableStore');

		return $store;
	}

	public function process() {
		$form = $this->ui->getWidget('indexform');
		$view = $this->ui->getWidget('view');
		$actions = $this->ui->getWidget('actions');

		if (!$form->process())
			return;

		if ($actions->selected == null)
			return;

		if (count($view->checked_items) == 0)
			return;

		switch ($actions->selected->name) {
			case 'delete':
				$this->app->replacePage('AdminSections/Delete');
				$this->app->page->items = $view->checked_items;
				break;

			case 'show':
				AdminDB::update($this->app->db, 'adminsections', 'hidden',
					false, 'boolean', 'sectionid', $view->checked_items);
				break;

			case 'hide':
				AdminDB::update($this->app->db, 'adminsections', 'hidden',
					true, 'boolean', 'sectionid', $view->checked_items);
				break;

			default:
				echo 'action = ', $actions->selected->name, '<br />';
				echo 'items = ';
				print_r($view->checked_items);
				echo '<br />';

				if ($actions->selected->widget != null)
					echo 'value = ', $actions->selected->widget->value;
		}
	}
}

?>
