<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatUI.php');

/**
 * Subclass of SwatUI for use in Admin.
 */
class AdminUI extends SwatUI {

	function __construct() {
		parent::__construct();

		$this->classmap = array('Admin' => 'Admin');
	}
}
