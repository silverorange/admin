<?php

require_once('Admin/AdminUI.php');
require_once('SwatDB/SwatDB.php');
require_once('Admin/AdminPage.php');

/**
 * Delete confirmation page for AdminSections
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminSectionsDelete extends AdminPage {

	/*
	 * The items to delete.
	 * @var Array
	 */
	public $items = null;

	private $ui;

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/Admin/confirmation.xml');
	}

	public function display() {
		$form = $this->ui->getWidget('confirmform');
		$form->action = $this->source;
		$form->addHiddenField('items', $this->items);

		$where_clause = 'sectionid in ('.implode(', ', $this->items).')';

		$items = SwatDB::getOptionArray($this->app->db, 'adminsections', 
			'title', 'sectionid', null, $where_clause);

		$message = $this->ui->getWidget('message');
		$message->content = '<ul><li>';
		$message->content .= implode('</li><li>', $items);
		$message->content .= '</li></ul>';

		$root = $this->ui->getRoot();
		$root->displayTidy();
	}

	public function process() {
		$form = $this->ui->getWidget('confirmform');

		if (!$form->process())
			return;

		if ($form->button->name == 'btn_yes') {
			$items = $form->getHiddenField('items');

			$sql = 'delete from adminsections where sectionid in (%s)';

			foreach ($items as &$id)
				$id = $this->app->db->quote($id, 'integer');

			$sql = sprintf($sql, implode(',', $items));
			$this->app->db->query($sql);
		}

		$this->app->relocate($this->app->getHistory());
	}
}

?>
