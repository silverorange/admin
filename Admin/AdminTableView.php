<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatTableView.php');
require_once('Admin/AdminTableViewRow.php');

/**
 * Subclass of SwatTableView for admin indexes.
 */
class AdminTableView extends SwatTableView {

    private $extra_rows;

	public function __init() {
		parent::init();
		$this->extra_rows = array();
	}

	public function appendRow(AdminTableViewRow $row) {
		$this->extra_rows[] = $row;
	}

	protected function displayContent() {
		parent::displayContent();

		foreach ($this->extra_rows as $row)
			$row->display($this->columns);
	}
}
