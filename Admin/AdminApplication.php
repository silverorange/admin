<?
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatApplication.php');
require_once("MDB2.php");
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

		//$this->queryForPage($source);

		/*
		if (!$this->isLoggedIn()) {
			$component = 'login';
			$subcomponent = 'index';
		} elseif (strpos($source, '/')) {
		*/
		if (strpos($source, '/')) {
			list($component, $subcomponent) = explode('/', $source);
		} else {
			$component = $source;
			$subcomponent = 'Index';
		}

		echo "$component/$subcomponent<br>";
		//if ($component == 'login')
		//	$class = 'Admin/Login/Index';

		require_once("Admin/$component/$subcomponent.php");
		$classname = $component.$subcomponent;
		$page = eval(sprintf("return new %s();", $classname));

		return $page;
	}

	private function queryForPage($source) {
		$sql = <<<SQL
			SELECT adminarticles.*, adminsections.title AS section_title
			FROM adminarticles
			INNER JOIN adminsections ON section = sectionID
			WHERE adminarticles.hidden = 0
				AND shortname = '$source'
				AND articleID IN (
					SELECT article
					FROM adminarticle_admingroup
					INNER JOIN adminuser_admingroup
						ON adminarticle_admingroup.groupnum = adminuser_admingroup.groupnum
					WHERE adminuser_admingroup.usernum = {$_SESSION['userID']}
				)
SQL;

		$result = $this->db->query($sql);
                                                                                                                             
		if ($result->size() == 0) {
			$sql = <<<SQL
				SELECT adminarticles.*, adminsections.title AS section_title
				FROM adminarticles
				INNER JOIN adminsections ON section = sectionID
				WHERE shortname = '$source_exp[0]'
					AND articleID IN (
						SELECT article
						FROM adminarticle_admingroup
						INNER JOIN adminuser_admingroup 
							ON adminarticle_admingroup.groupnum = adminuser_admingroup.groupnum
						WHERE adminuser_admingroup.usernum = {$_SESSION['userID']}
					)
SQL;
			$result = $this->db->query($sql);

			if ($result->numrows() == 0)
				$result = null;
		}

		return $result;
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
