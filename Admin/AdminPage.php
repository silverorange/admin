<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatPage.php');

/**
 * Abstract base class for admin pages.
 */
abstract class AdminPage extends SwatPage {

	/**
	 * @var title Title of the page.
	 */
	public $title = '';

	function __construct() {

	}

	abstract public function init($app);

	abstract public function display($app);
	
	abstract public function process($app);
}
