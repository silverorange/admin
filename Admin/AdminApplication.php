<?php

require_once 'Swat/SwatApplication.php';
require_once 'Swat/SwatMessage.php';
require_once 'MDB2.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/Admin.php';
require_once 'Admin/AdminPage.php';
require_once 'Admin/AdminPageRequest.php';
require_once 'Date.php';

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
		$this->base_uri_length = 4;
	}

	/**
	 * Get the page object
	 *
	 * Uses the $_GET variables to decide which page subclass to instantiate.
	 *
	 * @return AdminPage A subclass of {@link AdminPage} is returned.
	 */
	public function getPage($source = null) {
		
		$request = $this->getRequest($source);
		
		if ($request === null)
			$err = new SwatMessage(Admin::_('Component not found.'));
		else {
			$file = $request->getFilename();
			
			if ($file === null)
				$err = new SwatMessage(Admin::_('File not found.'));
			
			else {
				require_once $file;

				$classname = $request->getClassname();
				if ($classname === null)
					$err = new SwatMessage(
						sprintf(Admin::_('Class \'%s\' does not exist in the included file.'),
							$request->component.$request->subcomponent));
				else {
					$page = new $classname();
					$page->title = $request->title;
					$page->source = $request->source;
					$page->component = $request->component;
					$page->subcomponent = $request->subcomponent;
					$page->app = $this;
					$page->navbar->add(Admin::_('Home'), '');
					$page->navbar->add($request->title,
						($request->subcomponent == 'Index') ? null : $request->component);
				}	
			}
		}
	
		if (!isset($page)) {
			require_once 'Admin/NotFound.php';
			$page = new AdminNotFound();
			$page->app = $this;
			$page->source = 'Admin/NotFound';
			$page->title = Admin::_('Page not found');
			$page->component = 'Admin';
			$page->subcomponent = 'NotFound';
			$page->setMessage($err);
			$page->navbar->add(Admin::_('Home'), '');
		}
			
		if (isset($_SERVER['HTTP_REFERER']))
			$this->storeHistory($_SERVER['HTTP_REFERER']);

		return $page;
	}

	private function getRequest($source) {
		$request = null;
	
		if ($source === null) {
			if (isset($_GET['source']))
				$source = $_GET['source'];
			else
				$source = 'Admin/Front';
		}

		if ($this->isLoggedIn()) {
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
					'Front'   => Admin::_('Index'));

				$request = new AdminPageRequest();
				$request->title = $admin_titles[$subcomponent];
				$request->component = $component;
				$request->subcomponent = $subcomponent;
				
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

	private function queryForPage($component) {
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

	private function initDatabase() {
		// TODO: change to array /form of DSN and move parts to a secure include file.
		$dsn = "pgsql://php:test@zest/".$this->dbname;
		$this->db = MDB2::connect($dsn);
		$this->db->options['debug'] = true;

		if (MDB2::isError($this->db))
			throw new Exception('Unable to connect to database.');
	}

	private function initSession() {
		session_cache_limiter('');
		session_save_path('/so/phpsessions/'.$this->id);
		session_name($this->id);
		session_start();

		if (!isset($_SESSION['userID'])) {
			$_SESSION['userID'] = 0;
			$_SESSION['name'] = '';
			$_SESSION['username'] = '';
			$_SESSION['history'] = array();
		} elseif ($_SESSION['userID'] != 0) {	
			setcookie($this->id.'_username', $_SESSION['username'], time() + 86400, '/', '', 0);
		}
	}

	public function storeHistory($url) {
		$history = &$_SESSION['history'];

		if (!is_array($history))
			$history = array();

		$has_querystring = strpos($url, '?');
	
		if (count($history) > 0) {
			end($history);
			$last = current($history);
			$pos = strpos($last, '?');

			if ($pos)
				$last = substr($last, 0, $pos);
		} else {
			$last = null;
		}

		$pos = strpos($url, '?');

		if ($pos)
			$base = substr($url, 0, $pos);
		else
			$base = $url;

		if ($has_querystring || strcmp($last, $base) != 0) {
			array_push($history, $url);
		}

		// throw away old ones
		while (count($history) > 10)
			array_shift($history);

	}

	public function getHistory($index = 1) {

		for ($i = 0; $i <= $index; $i++)
			$url = array_pop($_SESSION['history']);

		return $url;
	}

	public function addMessage(SwatMessage $message) {

		if (!isset($_SESSION['messages']) || !is_array($_SESSION['messages']))
			$_SESSION['messages'] = array();

		$_SESSION['messages'][] = $message;
	}

	public function getMessages() {

		if (!isset($_SESSION['messages']) || !is_array($_SESSION['messages']))
			$_SESSION['messages'] = array();

		$ret = $_SESSION['messages'];
		$_SESSION['messages'] = array();
		return $ret;
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
		
		$sql = "select userid, name, username from adminusers
				where username = %s and password = %s and enabled = %s";

		$sql = sprintf($sql, 
			$this->db->quote($username, 'text'),
			$this->db->quote($md5_password, 'text'),
			$this->db->quote(true, 'boolean'));

		$rs = $this->db->query($sql);
		
		if ($rs->numRows()) {
			$result = $rs->fetchRow(MDB2_FETCHMODE_OBJECT); 
			$_SESSION['userID'] = $result->userid;
			$_SESSION['name']   = $result->name;
			$_SESSION['username']   = $result->username;

			$this->insertUserHistory($result->userid);

			return true;
		} else {
			return false;
		}
	}

	private function insertUserHistory($userid) {
		$user_agent = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : null;
		$remote_ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : null;
		$login_date = new Date();
		$login_date->toUTC();

		SwatDB::insertRow($this->db, 'adminuserhistory',
			array('integer:usernum','date:logindate', 'loginagent', 'remoteip'),
			array('usernum' => $userid, 'logindate' => $login_date->getDate(),
				'loginagent' => $user_agent, 'remoteip' => $remote_ip));
	}

	/**
	 * Set the user as logged-out 
	 */
	public function logout() {
		$_SESSION = array();
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

?>
