<?php

require_once('Swat/SwatPage.php');
require_once('Admin/AdminMenu.php');

/**
 * Page of an administrator
 *
 * Abstract base class for administrator pages.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminPage extends SwatPage {

	/**
	 * Source of this page
	 * @var string
	 */
	public $source;

	/**
	 * Component name of this page
	 * @var string
	 */
	public $component;

	/**
	 * Subcomponent name of this page
	 * @var string
	 */
	public $subcomponent;

	/**
	 * Initialize the page
	 *
	 * Sub-classes should implement this method to initialize the page. This 
	 * method should be called before either {@link AdminPage::display()} or 
	 * {@link AdminPage::process()}.
	 */
	abstract public function init();

	/**
	 * Display the page
	 *
	 * Sub-classes should implement this method to display the contents of 
	 * the page. Called after {@link AdminPage::init()}
	 */
	abstract public function display();
	
	/**
	 * Process the page
	 *
	 * Sub-classes should implement this method to process the page.
	 * Called after {@link AdminPage::init()}
	 */
	abstract public function process();

	/**
	 * Display admin page header
	 *
	 * Display common elements for the header of an admin page. Sub-classes
	 * should call this from their implementation of {@link AdminPage::display()}.
	 */
	public function displayHeader() {
		/**
		 * TODO: make these links work
		 */
		echo '<h1>', $this->app->title, '</h1>';
		echo '<div id="admin-syslinks">';
		echo 'Welcome <a href="Admin/Profile">'.$_SESSION['name'].'</a> &nbsp;|&nbsp;';
		echo '<a href="Admin/Profile">Customize</a> &nbsp;|&nbsp;';
		echo '<a href="Admin/Logout"><strong>Logout</strong></a>';
		echo '</div>';
	}

	/**
	 * Display admin page menu
	 *
	 * Display the menu of an admin page. Sub-classes should call this 
	 * from their implementation of {@link AdminPage::display()}.
	 */
	public function displayMenu() {
		$sql_userid = $this->app->db->quote($_SESSION['userID'], 'integer');

		$sql = "select * from sp_admin_menu(".$_SESSION['userID'].");";
		$types = array('text', 'text', 'integer', 'text', 'integer', 'text', 'text');
		$menu = $this->app->db->query($sql, $types, true, 'AdminMenu');
		$menu->display();	

	}
	
}
