<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatTableView.php');
require_once('Admin/AdminTableViewRow.php');
require_once('Admin/AdminTableViewRowCheckAll.php');

/**
 * Subclass of SwatTableView for admin indexes.
 */
class AdminTableView extends SwatTableView {

	/**
	 * Whether to show a "check all" widget.  For this option to work, the
	 * table view must contain a column named "checkbox".
	 * @var boolean
	 */
	public $show_checkall = true;

	/**
	 * The values of the checked checkboxes.  For this to be set, the table
	 * view must contain a SwatCellRendererCheckbox named "items".
	 * @var Array
	 */
	public $checked_items = array();

	private $extra_rows = array();

	public function init() {
		parent::init();

		if ($this->show_checkall)
			$this->appendRow(new AdminTableViewRowCheckAll());
	}

	protected function appendRow(AdminTableViewRow $row) {
		$this->extra_rows[] = $row;
	}

	protected function displayContent() {
		parent::displayContent();

		foreach ($this->extra_rows as $row)
			$row->display($this->columns);
	}

	public function process() {
		if (isset($_POST['items']) && is_array($_POST['items']))
			$this->checked_items = $_POST['items'];
	}
}
