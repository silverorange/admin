<?php

require_once('Admin/AdminUI.php');
require_once('Admin/AdminDB.php');
require_once('Admin/AdminPage.php');
require_once('MDB2.php');

class AdminSectionsDelete extends AdminPage {

	public $items = null;

	private $ui;

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/confirmation.xml');
	}

	public function display() {
		$form = $this->ui->getWidget('confirmform');
		$form->action = $this->source;

		$message = $this->ui->getWidget('message');
		$message->content = implode(', ', $this->items);

		$root = $this->ui->getRoot();
		$root->displayTidy();
	}

	public function process() {
		$form = $this->ui->getWidget('confirmform');

		if (!$form->process())
			return;

		if ($form->button->name == 'btn_yes') {
			
		}

		$this->app->relocate($this->component);
	}
}

?>
