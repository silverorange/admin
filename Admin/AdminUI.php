<?php

require_once('Swat/SwatUI.php');

/**
 * UI manager for administrators
 *
 * Subclass of {@link SwatUI} for use with the Admin package.  This can be used
 * as a central place to add {@link SwatUI::$classmap class maps} and 
 * {@link SwatUI::registerHandler() UI handlers} that are specific to the Admin 
 * package.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminUI extends SwatUI {

	/**
	 * Create a new AdminUI object
	 */
	public function __construct() {
		parent::__construct();

		$this->classmap = array('Admin' => 'Admin');
	}
}
