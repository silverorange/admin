<?php

require_once('Swat/SwatPage.php');
require_once('Swat/SwatNavBar.php');
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
	 * Navbar of this page
	 * @var SwatNavBar
	 */
	public $navbar;

	protected $ui = null;
	 
	public function __construct() {
		parent::__construct();

		$this->navbar = new SwatNavBar();
	}

	/**
	 * Initialize the page
	 *
	 * Sub-classes should implement this method to initialize the page. This 
	 * method should be called before either {@link AdminPage::display()} or 
	 * {@link AdminPage::process()}.
	 */
	abstract protected function init();

	/**
	 * Initialize the page before display
	 *
	 * Sub-classes should implement this method to initialize the page before display.
	 * This method should be called before {@link AdminPage::display()} and always be
	 * followed by a call to {@link AdminPage::display()}.
	 */
	public function displayInit() {

	}

	/**
	 * Display the page
	 *
	 * Sub-classes should implement this method to display the contents of 
	 * the page. Called after {@link AdminPage::init()}
	 */
	public function display() {
		if ($this->ui !== null) {
			$root = $this->ui->getRoot();
			$root->display();
		}
	}
	
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

	public function displayNavBar() {
		$this->navbar->display();	
	}

	/**
	 * Display admin page menu
	 *
	 * Display the menu of an admin page. Sub-classes should call this 
	 * from their implementation of {@link AdminPage::display()}.
	 */
	public function displayMenu() {
		$db = $this->app->db;
		$sql_userid = $db->quote($_SESSION['userID'], 'integer');
		
		$types = array('text', 'text', 'integer', 'text', 'integer', 'text', 'text');

		$menu = $db->executeStoredProc('sp_admin_menu', array($sql_userid),
					$types, true, 'AdminMenu');

		$menu->display();
	}
	
	protected function displayInitMessages() {
		$message_box = $this->ui->getWidget('message_box', true);
		$messages = $this->app->getMessages();

		if ($message_box !== null)
			$message_box->messages = $messages;
	}
}
