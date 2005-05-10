<?php

//TODO: update this file. It's using the old system an should extend AdminIndex

require_once('Admin/AdminUI.php');
require_once('SwatDB/SwatDB.php');
require_once('Admin/AdminPage.php');
require_once('Admin/AdminTableStore.php');

/**
 * Index page for AdminSections
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminSectionsIndex extends AdminPage {

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminSections/index.xml');

		$color_flydown = $this->ui->getWidget('color');
		$color_flydown->options = 
			array(0 => _('red'), 1 => _('yellow'), 2 => _('blue'));
	}

	public function display() {
		$view = $this->ui->getWidget('index_view');
		$view->model = $this->getTableStore();

		$form = $this->ui->getWidget('index_form');
		$form->action = $this->source;

		$root = $this->ui->getRoot();
		$root->display();
	}

	private function getTableStore() {
		$view = $this->ui->getWidget('index_view');

		$sql = 'select sectionid, title, show 
				from adminsections 
				order by displayorder';

		$types = array('integer', 'text', 'boolean');
		$store = $this->app->db->query($sql, $types, true, 'AdminTableStore');

		return $store;
	}

	public function process() {
		$form = $this->ui->getWidget('index_form');
		$view = $this->ui->getWidget('index_view');
		$actions = $this->ui->getWidget('index_actions');

		if (!$form->process())
			return;

		if ($actions->selected === null)
			return;

		if (count($view->checked_items) == 0)
			return;

		$num = count($view->checked_items);
		
		switch ($actions->selected->id) {
			case 'delete':
				$this->app->replacePage('AdminSections/Delete');
				$this->app->page->items = $view->checked_items;
				break;

			case 'show':
				SwatDB::updateColumn($this->app->db, 'adminsections', 
					'boolean:show', true, 'sectionid', 
					$view->checked_items);

				$msg = new SwatMessage(sprintf(_nS("%d section has been shown.", 
					"%d sections have been shown.", $num), $num));

				break;

			case 'hide':
				SwatDB::updateColumn($this->app->db, 'adminsections', 
					'boolean:show', false, 'sectionid', 
					$view->checked_items);

				$msg = new SwatMessage(sprintf(_nS("%d section has been hidden.", 
					"%d sections have been hidden.", $num), $num));

				break;

			default:
				echo 'action = ', $actions->selected->id, '<br />';
				echo 'items = ';
				print_r($view->checked_items);
				echo '<br />';

				if ($actions->selected->widget !== null)
					echo 'value = ', $actions->selected->widget->value;
		}
		
		if ($msg !== null)
			$this->app->addMessage($msg);
	}
}

?>
