<?php

require_once 'Swat/SwatApplication.php';
require_once 'MDB2.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/Admin.php';
require_once 'Admin/AdminSessionModule.php';
require_once 'Admin/AdminMessagesModule.php';
require_once 'Admin/AdminDatabaseModule.php';
require_once 'Admin/AdminPageRequest.php';
require_once 'Admin/AdminMenuStore.php';
require_once 'Admin/AdminMenuView.php';
require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/exceptions/AdminUserException.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';

/**
 * Web application class for an administrator
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminApplication extends SwatApplication
{
    // {{{ public properties
	
	/**
	 * A visble title for this admin
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Convenience reference to the built-in AdminSessionModule
	 *
	 * @var AdminSessionModule (readonly)
	 */
	public $session;

	/**
	 * Convenience reference to the built-in AdminMessagesModule
	 *
	 * @var AdminMessagesModule (readonly)
	 */
	public $messages;

	/**
	 * Convenience reference to the built-in AdminDatabaseModule
	 *
	 * @var AdminDatabaseModule (readonly)
	 */
	public $database;

	/**
	 * Convenience reference to MDB2 object within the built-in AdminDatabaseModule
	 *
	 * @var MDB2_Connection Database connection object (readonly)
	 */
	public $db;

	/**
	 * Source of the front page.
	 *
	 * @var string the page to load as the front page of the admin.
	 */
	public $front_source = 'AdminSite/Front';

	/**
	 * Default locale.
	 *
	 * @var string the locale to use by default (xx_XX).
	 */
	public $default_locale = null;

	// }}}
	// {{{ protected properties

	/**
	 * This application's menu view
	 *
	 * @var AdminMenuView
	 */
	protected $menu = null;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new application object
     *
     * @param string $id a unique identifier for this application.
     */
    public function __construct($id)
    {
		parent::__construct($id);

		$this->addModule(new AdminSessionModule($this));
		$this->addModule(new AdminMessagesModule($this));
		$this->addModule(new AdminDatabaseModule($this));

		// set up convenience references
		$this->session = $this->modules['AdminSessionModule'];
		$this->messages = $this->modules['AdminMessagesModule'];
		$this->database = $this->modules['AdminDatabaseModule'];
	}

    // }}}
    // {{{ public function init()

	/**
	 * Initialize the application
	 */
	public function init()
	{
		if ($this->default_locale !== null)
			setlocale(LC_ALL, $this->default_locale);

		$this->initBaseHref(4);
		$this->initModules();

		// set up convenience references
		$this->db = $this->database->mdb2;

		$this->initMenu();

		// call this last
		try {
			$this->initPage();
		} catch (AdminUserException $e) {
			$this->replacePage('AdminSite/Exception');
			$this->page->setException($e);
			$this->initPage();
		}
	}

    // }}}
    // {{{ public function run()

	/**
	 * Run the application
	 */
	public function run()
	{
		try {
			$this->getPage()->process();
			$this->getPage()->build();
		} catch (AdminException $e) {
			$this->replacePage('AdminSite/Exception');
			$this->page->setException($e);
			$this->page->build();
		}

		$this->getPage()->layout->display();
	}

    // }}}
    // {{{ public function resolvePage()

	/**
	 * Get the page object
	 *
	 * Uses the $_GET variables to decide which page subclass to instantiate.
	 *
	 * @return AdminPage A subclass of {@link AdminPage} is returned.
	 */
	public function resolvePage()
	{
		$source = self::initVar('source', null, self::VAR_GET);

		try {
			$page = $this->instantiatePage($source);
		} catch (AdminException $e) {
			$page = $this->instantiatePage('AdminSite/Exception');
			$page->setException($e);
		}

		return $page;
	}
		
    // }}}
    // {{{ public function replacePage()

	/**
	 * Replace the page object
	 *
	 * This method can be used to load another page to replace the current 
	 * page. For example, this is used to load a confirmation page when 
	 * processing an admin index page.
	 */
	public function replacePage($source)
	{
		$newpage = $this->instantiatePage($source);
		$_GET = array();
		$_POST = array();
		$this->setPage($newpage);
	}

    // }}}
    // {{{ protected function getServerName()
	/*
    protected function getServerName()
    {
        return ($this->live) ? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
    }
	*/

    // }}}
    // {{{ public function instantiatePage()

	public function instantiatePage($source)
	{
		if ($source === 'index.html')
			$source = $this->front_source;

		$request = $this->getRequest($source);
		
		if ($request === null)
			throw new AdminNotFoundException(
				sprintf(Admin::_("Component not found for source '%s'."), $source));

		$file = $request->getFilename();
			
		if ($file === null)
			throw new AdminNotFoundException(
				sprintf(Admin::_("File not found for source '%s'."), $source));
			
		require_once $file;
		$classname = $request->getClassname();

		if ($classname === null)
			throw new AdminNotFoundException(
				sprintf(Admin::_("Class '%s' does not exist in the included file."),
					$request->component.$request->subcomponent));

		$page = new $classname($this);
		$page->title = $request->title;

		if ($page instanceof AdminPage) {
			$page->source = $request->source;
			$page->component = $request->component;
			$page->subcomponent = $request->subcomponent;
			$page->navbar->addEntry(new AdminImportantNavBarEntry($this->title, ''));
			$page->navbar->addEntry(new SwatNavBarEntry($request->title, 
				($request->subcomponent == 'Index') ? null : $request->component));
		}

		return $page;
	}

    // }}}
    // {{{ public function instantiateMenu()

	/**
	 * Creates and returns the menu view for this application
	 *
	 * Admin applications that want a custom menu should over-ride this method.
	 *
	 * @param AdminMenuStore $menu_store the menu data object to view.
	 *
	 * @return AdminMenuView
	 */
	public function instantiateMenu(AdminMenuStore $menu_store)
	{
		$menu_view = new AdminMenuView($menu_store);

		return $menu_view;
	}

    // }}}
	// {{{ protected function initMenu()

	/**
	 * Initializes this application's menu view
	 */
	protected function initMenu()
	{
		if ($this->menu === null) {
			$menu_store = SwatDB::executeStoredProc($this->db,
				'sp_admin_menu', $this->db->quote($_SESSION['user_id'],
				'integer'), 'AdminMenuStore');

			$this->menu = $this->instantiateMenu($menu_store);
		}
	}

	// }}}
	// {{{ public function getMenuView()

	/**
	 * Gets this application's menu view
	 *
	 * @return AdminMenuView the menu view of this application.
	 */
	public function getMenuView()
	{
		return $this->menu;
	}

	// }}}
    // {{{ private function getRequest()

	private function getRequest($source)
	{
		$request = null;

		if ($source === null)
			$source = $this->front_source;

		if ($this->session->isLoggedIn()) {
			$source_exp = explode('/', $source);
			
			if (count($source_exp) == 1) {
				$component = $source;
				$subcomponent = 'Index';
			} elseif (count($source_exp) == 2) {
				list($component, $subcomponent) = $source_exp;
			} else {
				return null;
			}

			if ($component == 'AdminSite') {
				$admin_titles = array(
					'Profile'        => Admin::_('Edit User Profile'),
					'Logout'         => Admin::_('Logout'),
					'Login'          => Admin::_('Login'),
					'Exception'      => Admin::_('Exception'),
					'Front'          => Admin::_('Index'),
					'MenuViewServer' => Admin::_(''));

				if (isset($admin_titles[$subcomponent])) {
					$request = new AdminPageRequest();
					$request->title = $admin_titles[$subcomponent];
					$request->component = $component;
					$request->subcomponent = $subcomponent;
				} else {
					return null;
				}
				
			} else {
			
				$row = $this->queryForPage($component);

				if ($row !== null) {
					$request = new AdminPageRequest();
					$request->title = $row->component_title;
					$request->component = $component;
					$request->subcomponent = $subcomponent;
				} else {
					return null;
				}
			}

		} else {
			$request = new AdminPageRequest();
			$request->component = 'AdminSite';
			$request->subcomponent = 'Login';
			$request->title = Admin::_('Login');
		}

		$request->source = $source;

		return $request;
	}

    // }}}
    // {{{ private function queryForPage()

	private function queryForPage($component)
	{
		// TODO: move this page query into a stored procedure
		$sql = "SELECT admincomponents.title as component_title, admincomponents.shortname,
				adminsections.title as section_title
			FROM admincomponents
				INNER JOIN adminsections ON admincomponents.section = adminsections.id
			WHERE admincomponents.enabled = %s
				AND admincomponents.shortname = %s
				AND admincomponents.id IN (
					SELECT component
					FROM admincomponent_admingroup
					INNER JOIN adminuser_admingroup
						ON admincomponent_admingroup.groupnum = adminuser_admingroup.groupnum
					WHERE adminuser_admingroup.usernum = %s
				)";

		$sql = sprintf($sql,
			$this->db->quote(true, 'boolean'),
			$this->db->quote($component, 'text'),
			$this->db->quote($_SESSION['user_id'], 'integer'));	

		$row = SwatDB::queryRow($this->db, $sql);
		return $row;
	}

    // }}}
}

?>
