<?php
require_once('Swat/SwatPagination.php');

/**
 * An orderable column
 *
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminPagination extends SwatPagination {

	public function display() {
		$this->href = $_GET['source'];
		$this->unset_get_vars = array('source');
		parent::display();
	}

}
