<?

require_once('Swat/SwatApplication.php');
require_once('MDB2.php');
require_once('AdminPage.php');

/**
 * Web application class for an administrator
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminApplication extends SwatApplication {
	
	/**
	 * A visble title for this admin
	 * @var string
	 */
	public $title;

	/**
	 * Name of the database
	 *
	 * This is the name of the database to connect to.  Set this before calling
	 * {@link AdminApplication::init()}, afterwords consider it readonly.
	 *
	 * @var string
	 */
	public $dbname;

	/**
	 * The database object
	 * @var MDB2_Connection Database connection object (readonly)
	 */
	public $db = null;
	
	/**
	 * The page object
	 * @var AdminPage Current page object (readonly)
	 */
	public $page = null;

	/**
	 * Replace the page object
	 *
	 * This method can be used to load another page to replace the current 
	 * page. For example, this is used to load a confirmation page when 
	 * processing an admin index page.
	 *
	 * @return AdminPage A subclass of {@link AdminPage} is returned.
	 */
	public function replacePage($source) {
		$this->page = $this->getPage($source);
		$this->page->init();
	}

	/**
	 * Initialize the application
	 */
	public function init() {
		$this->initDatabase();
		$this->initSession();
		$this->initUriVars(4);
	}

	/**
	 * Get the page object
	 *
	 * Uses the $_GET variables to decide which page subclass to instantiate.
	 *
	 * @return AdminPage A subclass of {@link AdminPage} is returned.
	 */
	public function getPage($source = null) {

		if ($source == null) {
			if (isset($_GET['source']))
				$source = $_GET['source'];
			else
				$source = 'Front';
		}

		$found = true;

		if ($this->isLoggedIn()) {
			if (strpos($source, '/')) {
				list($component, $subcomponent) = explode('/', $source);
			} else {
				$component = $source;
				$subcomponent = 'Index';
			}
			
			$pagequery = $this->queryForPage($component);

			if ($pagequery->numRows()) {
				$row = $pagequery->fetchRow(MDB2_FETCHMODE_OBJECT);
				$title = $row->component_title;
			} else {
				// TODO: setting false here should cause the page to NOT load.
				$found = false;
				$title = '';
			}
		
		} else {
			$component = 'Admin';
			$subcomponent = 'Login';
			$title = _S("Login");
		}

		$classfile = $component.'/'.$subcomponent.'.php';
		$file = null;

		if (file_exists('../../include/admin/'.$classfile)) {
			$file = '../../include/admin/'.$classfile;
		} else {
			$paths = explode(':', ini_get('include_path'));

			foreach ($paths as $path) {
				if (file_exists($path.'/Admin/'.$classfile)) {
					$file = $classfile;
					break;
				}
			}
		}

		if ($file != null)
			require_once($file);

		$classname = $component.$subcomponent;

		if (!class_exists($classname)) {
			$component = 'Admin';
			$subcomponent = 'NotFound';
			$file = $component.'/'.$subcomponent.'.php';
			require_once($file);
			$classname = $component.$subcomponent;
			$title = _S("Page Not Found");	
		}
	
		$page = eval(sprintf("return new %s();", $classname));
		$page->title = $title;
		$page->source = $source;
		$page->component = $component;
		$page->subcomponent = $subcomponent;
		$page->app = $this;

		return $page;
	}

	private function queryForPage($component) {
		$shortname = $this->db->quote($component, 'text');
		$hidden = $this->db->quote(false, 'boolean');
		$usernum = $this->db->quote($_SESSION['userID'], 'integer');	

		// TODO: move this page query into a stored procedure
		$sql = "SELECT admincomponents.title as component_title, admincomponents.shortname,
				adminsections.title as section_title
			FROM admincomponents
				INNER JOIN adminsections ON admincomponents.section = adminsections.sectionid
			WHERE admincomponents.hidden = {$hidden}
				AND admincomponents.shortname = {$shortname}
				AND componentid IN (
					SELECT component
					FROM admincomponent_admingroup
					INNER JOIN adminuser_admingroup
						ON admincomponent_admingroup.groupnum = adminuser_admingroup.groupnum
					WHERE adminuser_admingroup.usernum = {$usernum}
				)";

		$rs = $this->db->query($sql, array('text', 'text'));
		
		if (MDB2::isError($rs))
			throw new Exception($rs->getMessage());
			
		return $rs;

	}

	private function initDatabase() {
		// TODO: change to array form of DSN and move parts to a secure include file.
		$dsn = "pgsql://php:test@zest/".$this->dbname;
		$this->db = MDB2::connect($dsn);

		if (MDB2::isError($this->db))
			throw new Exception('Unable to connect to database.');
	}

	private function initSession() {
		session_cache_limiter('');
		session_save_path('/so/phpsessions/'.$this->name);
		session_name($this->name);
		session_start();

		if (!isset($_SESSION['userID'])) {
			$_SESSION['userID'] = 0;
			$_SESSION['name'] = '';
		}
	}

	/**
	 * Authenticate user
	 * @param string $username
	 * @param string $password
	 * @return bool True if login is successful.
	 */
	public function login($username, $password) {
		$this->logout(); //make sure user is logged out before logging in
	
		$md5_password = md5($password);
		
		$sql = "select userid,name from adminusers
				where username = %s and password = %s and enabled = %s";

		$sql = sprintf($sql, 
			$this->db->quote($username, 'text'),
			$this->db->quote($md5_password, 'text'),
			$this->db->quote(true, 'boolean'));

		$rs = $this->db->query($sql, array('integer', 'text'));
		
		if ($rs->numRows()) {
			$result = $rs->fetchRow(MDB2_FETCHMODE_OBJECT); 
			$_SESSION['userID'] = $result->userid;
			$_SESSION['name']   = $result->name;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Set the user as logged-out 
	 */
	public function logout() {
		$_SESSION['userID'] = 0;
	}

	/**
	 * Check the user's logged-in status
	 * @return bool True if user is logged in. 
	 */
	public function isLoggedIn() {
		if (isset($_SESSION['userID']))
			return ($_SESSION['userID'] != 0);

		return false;
	}
}
