<?php

require_once 'Swat/SwatPage.php';
require_once 'Swat/SwatForm.php';
require_once 'Admin/AdminNavBar.php';
require_once 'Admin/AdminMenu.php';
require_once 'Admin/AdminUI.php';

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
	const RELOCATE_URL_FIELD = '_admin_relocate_url';

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

	 * @var AdminNavBar
	 */
	public $navbar;

	/**
	 * Title of this page
	 * @var Title
	 */
	public $title = null;

	// }}}
	// {{{ protected properties

	/**
	 * The user-interface of this page
	 *
	 * @var AdminUI
	 */
	protected $ui = null;

	// }}}
	// {{{ public function __construct()

	public function __construct($app)
	{
		parent::__construct($app);

		$this->navbar = new AdminNavBar();
		$this->ui = new AdminUI();
	}

	// }}}
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new SwatLayout('Admin/layouts/default.php');
	}

	// }}}
	// {{{ public function getRelativeURL()

	public function getRelativeURL()
	{
		$url = $this->source.'?';

		foreach ($_GET as $name => $value)
			if ($name != 'source')
				$url.= $name.'='.$value.'&';

		$url = substr($url, 0, -1);

		return $url;
	}

	// }}}
	// {{{ public function getRefererURL()

	public function getRefererURL()
	{
		if (isset($_SERVER['HTTP_REFERER'])) {
			return $_SERVER['HTTP_REFERER'];
		} else {
			$source_exp = explode('/', $this->source);
			return $source_exp[0];
		}
	}

	// }}}

	// init phase
	// {{{ public function init()

	/**
	 * Initialize the page
	 *
	 * Initializes {@link AdminPage::initInternal()} and {@link
	 * AdminPage::$ui}. Sub-classes should implement
	 * {@link SwatPage::initInternal()} to perform their own
	 * initialization. 
	 */
	public function init()
	{
		parent::init();
		$this->initInternal();
		$this->ui->init();
	}

	// }}}
	// {{{ protected function initInternal()

	/**
	 * Initialize the page
	 *
	 * Sub-classes should implement this method to initialize the page. At
	 * this point the {@link AdminPage::$ui} has been constructed but has not been
	 * initialized.
	 */
	protected function initInternal()
	{
	}

	// }}}

	// process phase
	// {{{ public function process()

	/**
	 * Process the page
	 *
	 * Sub-classes should implement this method to process the page.
	 * Sub-classes should call parent:process first which calls
	 * {@link AdminPage::$ui->process()}.
	 * Called after {@link AdminPage::init()}.
	 */
	public function process()
	{
		$this->ui->process();
		$this->processInternal();
	}

	// }}}
	// {{{ protected function processInternal()

	/**
	 * Processes the page
	 *
	 * Sub-classes should implement this method to process the page. At
	 * this point the {@link AdminPage::$ui} has already been processed.
	 */
	protected function processInternal()
	{
	}

	// }}}

	// build phase
	// {{{ public function build()

	public function build()
	{
		$this->buildInternal();

		ob_start();
		$this->ui->getRoot()->displayHtmlHeadEntries();
		$this->layout->html_head_entries = ob_get_clean();

		$page_title = ($this->title === null) ? $this->navbar->getLastEntry() : $this->title;
		$this->layout->title = $page_title.' - '.$this->app->title;
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
	// {{{ protected function buildInternal()

	/**
	 * Initialize the page before display
	 *
	 * Sub-classes should implement this method to initialize elements of
	 * the page. This method is called at the beginning of {@link
	 * AdminPage::build()}. This is useful to do database queries that are
	 * only needed for {@link AdminPage::display()} and not {@link
	 * AdminPage::process()}, while initialization needed for both display
	 * and process should be included in {@link AdminPage::init()}.
	 */
	protected function buildInternal()
	{
	}

	// }}}
	// {{{ protected function display()

	/**
	 * Display the page
	 *
	 * Sub-classes should implement this method to display the contents of 
	 * the page. Called after {@link AdminPage::init()}
	 */
	protected function display()
	{
		if ($this->ui !== null) {
			$this->ui->display();
		}
	}

	// }}}
	// {{{ protected function displayHeader()

	/**
	 * Display admin page header
	 *
	 * Display common elements for the header of an admin page. Sub-classes
	 * should call this from their implementation of {@link AdminPage::display()}.
	 */
	protected function displayHeader()
	{
		echo '<div id="admin-syslinks">';
		echo 'Welcome <a href="AdminSite/Profile">'.$_SESSION['name'].'</a> &nbsp;|&nbsp;';
		echo '<a href="AdminSite/Profile">Customize</a> &nbsp;|&nbsp;';
		echo '<a href="AdminSite/Logout"><strong>Logout</strong></a>';
		echo '</div>';
	}

	// }}}
	// {{{ protected function displayNavBar()

	protected function displayNavBar()
	{
		$this->navbar->display();	
	}

	// }}}
	// {{{ protected function displayMenu()

	/**
	 * Display admin page menu
	 *
	 * Display the menu of an admin page. Sub-classes should call this 
	 * from their implementation of {@link AdminPage::display()}.
	 */
	protected function displayMenu()
	{		
		$menu = SwatDB::executeStoredProc($this->app->db, 'sp_admin_menu',
				$this->app->db->quote($_SESSION['user_id'], 'integer'),
				'AdminMenu');

		$menu->display();
	}

	// }}}
	// {{{ protected function buildMessages()

	protected function buildMessages()
	{
		try {
			$message_display = $this->ui->getWidget('message_display');
			foreach ($this->app->messages->getAll() as $message)
				$message_display->add($message);

		} catch (SwatWidgetNotFoundException $e) {
		}
	}

	// }}}
}

?>
