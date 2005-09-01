<?php

require_once 'Swat/SwatApplication.php';
require_once 'MDB2.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/Admin.php';
require_once 'Admin/AdminApplicationHistoryModule.php';
require_once 'Admin/AdminApplicationSessionModule.php';
require_once 'Admin/AdminApplicationMessagesModule.php';
require_once 'Admin/AdminApplicationDatabaseModule.php';
require_once 'Admin/AdminPage.php';
require_once 'Admin/AdminPageRequest.php';

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
	 * @var string
	 */
	public $title;

	/**
	 * Convenience reference to the built-in AdminApplicationHistoryModule
	 *
	 * @var AdminApplicationHistoryModule (readonly)
	 */
	public $history;

	/**
	 * Convenience reference to the built-in AdminApplicationSessionModule
	 *
	 * @var AdminApplicationSessionModule (readonly)
	 */
	public $session;

	/**
	 * Convenience reference to the built-in AdminApplicationMessagesModule
	 *
	 * @var AdminApplicationMessagesModule (readonly)
	 */
	public $messages;

	/**
	 * Convenience reference to the built-in AdminApplicationDatabaseModule
	 *
	 * @var AdminApplicationDatabaseModule (readonly)
	 */
	public $database;

	/**
	 * Convenience reference to MDB2 object within the built-in AdminApplicationDatabaseModule
	 *
	 * @var MDB2_Connection Database connection object (readonly)
	 */
	public $db;
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

		$this->addModule(new AdminApplicationHistoryModule($this));
		$this->addModule(new AdminApplicationSessionModule($this));
		$this->addModule(new AdminApplicationMessagesModule($this));
		$this->addModule(new AdminApplicationDatabaseModule($this));

		// set up convenience references
		$this->history = $this->modules['AdminApplicationHistoryModule'];
		$this->session = $this->modules['AdminApplicationSessionModule'];
		$this->messages = $this->modules['AdminApplicationMessagesModule'];
		$this->database = $this->modules['AdminApplicationDatabaseModule'];
	}

    // }}}
    // {{{ public function init()

	/**
	 * Initialize the application
	 */
	public function init()
	{
        $this->initBaseHref(4);
        $this->initModules();

		// set up convenience references
		$this->db = $this->database->mdb2;

		// call this last
        $this->initPage();
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
		return $this->instantiatePage($source);
	}
		
    // }}}
    // {{{ private function instantiatePage()

	private function instantiatePage($source)
	{
		$request = $this->getRequest($source);
		
		if ($request === null)
			$err = new SwatMessage(Admin::_('Component not found.'),
				SwatMessage::SYSTEM_ERROR);
		else {
			$file = $request->getFilename();
			
			if ($file === null)
				$err = new SwatMessage(Admin::_('File not found.'),
					SwatMessage::SYSTEM_ERROR);
			
			else {
				require_once $file;

				$classname = $request->getClassname();
				if ($classname === null) {
					$err = new SwatMessage(
						sprintf(Admin::_('Class \'%s\' does not exist in the included file.'),
							$request->component.$request->subcomponent),
						SwatMessage::SYSTEM_ERROR);
				} else {
					$page = new $classname($this);
					$page->title = $request->title;

					if ($page instanceof AdminPage) {
						$page->source = $request->source;
						$page->component = $request->component;
						$page->subcomponent = $request->subcomponent;
						// TODO: Make this first element an <h1>, but make sure the <h1> is outside the <a></a>
						$page->navbar->addElement($this->title, '');
						$page->navbar->addElement($request->title, 
							($request->subcomponent == 'Index') ? null : $request->component);
					}
				}	
			}
		}
	
		if (!isset($page)) {
			require_once 'Admin/NotFound.php';
			$page = new AdminNotFound($this);
			$page->source = 'Admin/NotFound';
			$page->title = Admin::_('Page not found');
			$page->component = 'Admin';
			$page->subcomponent = 'NotFound';
			$page->setMessage($err);
			$page->navbar = new SwatNavBar();
			$page->navbar->addElement($this->title, '');
		}
			
		if (isset($_SERVER['HTTP_REFERER']))
			$this->history->storeHistory($_SERVER['HTTP_REFERER']);

		return $page;
	}

    // }}}
    // {{{ private function getRequest()

	private function getRequest($source)
	{
		$request = null;
	
		if ($source === null) {
			if (isset($_GET['source']))
				$source = $_GET['source'];
			else
				$source = 'Admin/Front';
		}

		if ($this->session->isLoggedIn()) {
			if (strpos($source, '/')) {
				list($component, $subcomponent) = explode('/', $source);
			} else {
				$component = $source;
				$subcomponent = 'Index';
			}

			if ($component == 'Admin') {
				$admin_titles = array(
					'Profile' => Admin::_('Edit User Profile'),
					'Logout'  => Admin::_('Logout'),
					'Login'   => Admin::_('Login'),
					'NoAccess'   => Admin::_('No Access'),
					'NotFound'   => Admin::_('Not Found'),
					'Front'   => Admin::_('Index'));

				if (isset($admin_titles[$subcomponent])) {
					$request = new AdminPageRequest();
					$request->title = $admin_titles[$subcomponent];
					$request->component = $component;
					$request->subcomponent = $subcomponent;
				} else
					return null;
				
			} else {
			
				$pagequery = $this->queryForPage($component);

				if ($pagequery->numRows()) {
					$row = $pagequery->fetchRow(MDB2_FETCHMODE_OBJECT);
					$request = new AdminPageRequest();
					$request->title = $row->component_title;
					$request->component = $component;
					$request->subcomponent = $subcomponent;
				} else
					return null;
			}

		} else {
			$request = new AdminPageRequest();
			$request->component = 'Admin';
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
		$shortname = $this->db->quote($component, 'text');
		$enabled = $this->db->quote(true, 'boolean');
		$usernum = $this->db->quote($_SESSION['userID'], 'integer');	

		// TODO: move this page query into a stored procedure
		$sql = "SELECT admincomponents.title as component_title, admincomponents.shortname,
				adminsections.title as section_title
			FROM admincomponents
				INNER JOIN adminsections ON admincomponents.section = adminsections.sectionid
			WHERE admincomponents.enabled = {$enabled}
				AND admincomponents.shortname = {$shortname}
				AND componentid IN (
					SELECT component
					FROM admincomponent_admingroup
					INNER JOIN adminuser_admingroup
						ON admincomponent_admingroup.groupnum = adminuser_admingroup.groupnum
					WHERE adminuser_admingroup.usernum = {$usernum}
				)";

		$rs = $this->db->query($sql);
		
		if (MDB2::isError($rs))
			throw new Exception($rs->getMessage());
			
		return $rs;

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
		$this->setPage($newpage);
	}

    // }}}
    // {{{ public function replacePageNoAccess()

	/**
	 * Replace Page with No Access Admin Page
	 *
	 * This method is used to replace the current page with a No Access page
	 * and an optional message.
	 *
	 * @param SwatMessage An optional {@link SwatMessage} to display.
	 */
	public function replacePageNoAccess($msg = null)
	{
		$this->replacePage('Admin/NoAccess');
		$this->page->setMessage($msg);
		$this->page->build();
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
}

?>
