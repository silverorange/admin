<?php

require_once 'Swat/SwatPage.php';
require_once 'Swat/SwatNavBar.php';
require_once 'Admin/AdminMenu.php';

/**
 * Page of an administrator
 *
 * Abstract base class for administrator pages.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminPage extends SwatPage
{
    // {{{ public properties

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

	/**
	 * Title of this page
	 * @var Title
	 */
	public $title;

    // }}}
    // {{{ protected properties

	protected $ui = null;

    // }}}
    // {{{ public function createLayout()

    protected function createLayout()
    {
        return new SwatLayout('Admin/layouts/default.php');
    }

    // }}}
    // {{{ public function initDisplay()

	/**
	 * Initialize the page before display
	 *
	 * Sub-classes should implement this method to initialize the page before display.
	 * This method should be called before {@link AdminPage::display()} and always be
	 * followed by a call to {@link AdminPage::display()}.
	 */
	public function initDisplay()
	{
	}

    // }}}
    // {{{ public function build()

	public function build()
	{
		$this->initDisplay();

		$this->layout->title = $this->app->title.' | '.$this->title;
		$this->layout->basehref = $this->app->getBaseHref();

		ob_start();
		$this->displayHeader();
		$this->layout->header = ob_get_clean();

		ob_start();
		$this->navbar->display();	
		$this->layout->navbar = ob_get_clean();

		ob_start();
		$this->displayMenu();
		$this->layout->menu = ob_get_clean();

		ob_start();
		$this->display();
		$this->layout->content = ob_get_clean();
	}

    // }}}
    // {{{ public function display()

	/**
	 * Display the page
	 *
	 * Sub-classes should implement this method to display the contents of 
	 * the page. Called after {@link AdminPage::init()}
	 */
	public function display()
	{
		if ($this->ui !== null) {
			$this->ui->display();
		}
	}
	
    // }}}
	// {{{ abstract public function process()

	/**
	 * Process the page
	 *
	 * Sub-classes should implement this method to process the page.
	 * Called after {@link AdminPage::init()}
	 */
	abstract public function process();

    // }}}
    // {{{ public function displayHeader()

	/**
	 * Display admin page header
	 *
	 * Display common elements for the header of an admin page. Sub-classes
	 * should call this from their implementation of {@link AdminPage::display()}.
	 */
	public function displayHeader()
	{
		echo '<h1>', $this->app->title, '</h1>';
		echo '<div id="admin-syslinks">';
		echo 'Welcome <a href="Admin/Profile">'.$_SESSION['name'].'</a> &nbsp;|&nbsp;';
		echo '<a href="Admin/Profile">Customize</a> &nbsp;|&nbsp;';
		echo '<a href="Admin/Logout"><strong>Logout</strong></a>';
		echo '</div>';
	}

    // }}}
    // {{{ public function displayNavBar()

	public function displayNavBar()
	{
		$this->navbar->display();	
	}

    // }}}
    // {{{ public function displayMenu()

	/**
	 * Display admin page menu
	 *
	 * Display the menu of an admin page. Sub-classes should call this 
	 * from their implementation of {@link AdminPage::display()}.
	 */
	public function displayMenu()
	{		
		$menu = SwatDB::executeStoredProc($this->app->db, 'sp_admin_menu',
				$this->app->db->quote($_SESSION['userID'], 'integer'),
				'AdminMenu');

		$menu->display();
	}
	
    // }}}
    // {{{ protected function initMessages()

	protected function initMessages()
	{
		$message_box = $this->ui->getWidget('message_box', true);
		$messages = $this->app->messages->getAll();

		if ($message_box !== null)
			$message_box->messages = $messages;
	}

    // }}}
}

?>
