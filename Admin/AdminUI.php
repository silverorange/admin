<?php

require_once('Swat/SwatUI.php');

/**
 * UI manager for administrators
 *
 * Subclass of SwatUI for use with the Admin package.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminUI extends SwatUI {

	function __construct() {
		parent::__construct();

		$this->classmap = array('Admin' => 'Admin');
	}
}
