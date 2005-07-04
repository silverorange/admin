<?php

require_once 'Swat/SwatTableViewOrderableColumn.php';

/**
 * An orderable column
 *
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminTableViewOrderableColumn extends SwatTableViewOrderableColumn {

	public function displayHeader() {
		$this->link = $_GET['source'];
		$this->unset_get_vars = array('source');
		parent::displayHeader();
	}

}

?>
