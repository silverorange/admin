<?
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatApplication.php');
require_once("MDB2.php");
require_once('AdminPage.php');

class AdminApplication extends SwatApplication {

	public $db = null;

	function __construct() {
		$this->connect_db();

	}

	/**
	 * Get the page object.
	 * Uses the $_GET variables to decide which page subclass to instantiate.
	 * @return AdminPage A subclass of AdminPage is returned.
	 */
	public function get_page() {

		if (isset($_GET['source']))
			$source = $_GET['source'];
		else
			$source = 'Front';

		if (strpos($source, '/')) {
			list($component, $subcomponent) = explode('/', $source);
		} else {
			$component = $source;
			$subcomponent = 'Index';
		}


		echo "$component/$subcomponent<br>";

		switch ($component) {

			case 'AdminSections':
				require_once("Admin/$component/$subcomponent.php");
				$classname = $component.$subcomponent;
				$page = eval(sprintf("return new %s();", $classname));
				break;

			default:
				// TODO: instatiate a "Not Found" page here.
				echo 'not found';
				exit();

		}

		return $page;
	}

	private function connect_db() {
		// TODO: change to array form of DSN and move parts to a secure include file.
		$dsn = "pgsql://php:test@zest/silverorange2";
		$this->db = MDB2::connect($dsn);

		if (MDB2::isError($this->db))
			throw new Exception('Unable to connect to database.');
	}
}
