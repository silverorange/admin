<?
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatApplication.php');
require_once('MDB2.php');
require_once('AdminPage.php');

class AdminApplication extends SwatApplication {
	
	/**
	 * The database object.
	 * @var MDB2 connection object
	 */
	public $db = null;
	
	/**
	 * The name of the application
	 * @var string
	 */
	public $name;
	
	/**
	 * The name of the database to connect to.
	 * @var string
	 */
	public $dbname;

	/**
	 * The title used for display
	 * @var $title
	 */
	public $title;

	function __construct($name) {
		$this->name = $name;
		
	}

	/**
	 * Initialize the application.
	 * Initalize the database connection to the database named in $dbname, and
	 * the sessions for the application.
	 */
	function init() {
		$this->initDatabase();
		$this->initSession();

		$uri_array = explode('/', $_SERVER['REQUEST_URI']);
		$this->uri = implode('/', array_slice($uri_array, 0, 5)).'/';

		// TODO: Once we have a SITE_LIVE equivalent, we should use HTTP_HOST
		//       on stage and SERVER_NAME on live.
		$this->basehref = 'http://'.$_SERVER['HTTP_HOST'].$this->uri;
	}

	/**
	 * Get the page object.
	 * Uses the $_GET variables to decide which page subclass to instantiate.
	 * @return AdminPage A subclass of AdminPage is returned.
	 */
	public function getPage() {

		if (isset($_GET['source']))
			$source = $_GET['source'];
		else
			$source = 'front';


		$found = true;

		if (1 == 1 || $this->isLoggedIn()) {
			if (strpos($source, '/')) {
				list($component, $subcomponent) = explode('/', $source);
			} else {
				$component = $source;
				$subcomponent = 'Index';
			}
			
			$pagequery = $this->queryForPage($component);
			
			
			//if ($pagequery->size()) {
				$row = $pagequery->fetchRow(MDB2_FETCHMODE_OBJECT);
				$page_title = $row->component_title;
			//}
			
			/*
			if (!$pagequery->size())
				$found = false;
			*/
		
		} else {
			$component = 'Admin';
			$subcomponent = 'Login';
			$page_title = _S("Login");
		}

		$file = $component.'/'.$subcomponent.'.php';
		/*if (!file_exists($file)) {
				
				$component = 'Admin';
				$subcomponent = 'PageNotFound';
				$page_title = _S("Page Not Found");	
			
		}*/
		
		require_once($file);
	
		$classname = $component.$subcomponent;
		$page = eval(sprintf("return new %s();", $classname));
		$page->title = $page_title;

		return $page;
	}

	private function queryForPage($component) {
		// TODO: Figure out how MDB2 escaping boolean works
		// also need to add proper session stuff
		$shortname = $this->db->quote($component, 'text');
		//$hidden = $this->db->quote('N','boolean');
		$hidden = "'0'";
		//$usernum = $this->db->quote($_SESSION['userID'], 'integer');	
		$usernum = $this->db->quote(2, 'integer');		
		
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

		$result = $this->db->query($sql, array('text','text'));
		
		if (MDB2::isError($result))
            throw new Exception($result->getMessage());
		else
			return $result;

		//if ($result->numrows() == 0)
		//		$result = null;
		//}

		//return $result;
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

	public function isLoggedIn() {
			if (isset($_SESSION['userID']))
				return ($_SESSION['userID'] != 0);

			return false;
	}
}
