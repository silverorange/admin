<?php

require_once 'Site/SiteApplication.php';
require_once 'Site/SiteDatabaseModule.php';
require_once 'Site/SiteConfigModule.php';
require_once 'Site/SiteMessagesModule.php';
require_once 'MDB2.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/Admin.php';
require_once 'Admin/AdminSessionModule.php';
require_once 'Admin/AdminPageRequest.php';
require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/layouts/AdminLayout.php';
require_once 'Admin/exceptions/AdminUserException.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';

/**
 * Web application class for an administration application
 *
 * @package   Admin
 * @copyright 2004-2006 silverorange
 */
class AdminApplication extends SiteApplication
{
	// {{{ public properties
	
	/**
	 * A visble title for this admin
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Convenience reference to MDB2 object within the built-in AdminDatabaseModule
	 *
	 * @var MDB2_Connection Database connection object (readonly)
	 */
	public $db;

	/**
	 * Default locale
	 *
	 * This locale is used for translations, collation and locale-specific
	 * formatting. The locale is a five character identifier composed of a
	 * language code (ISO 639) an underscore and a country code (ISO 3166). For
	 * example, use 'en_CA' for Canadian English.
	 *
	 * @var string
	 */
	public $default_locale = null;

	/**
	 * Class to use for the menu.
	 *
	 * @var string the menu class name.
	 */
	public $menu_class = 'AdminMenuView';

	// }}}
    // {{{ public function __construct()

    public function __construct($id)
	{
		parent::__construct($id);

		$this->exception_page_source = 'AdminSite/Exception';
	}

	// }}}
	// {{{ protected properties

	/**
	 * Source of the front page.
	 *
	 * @var string the page to load as the front page of the admin.
	 */
	protected $front_source = 'AdminSite/Front';

	// }}}

	// {{{ public function run()

	public function run()
	{
		if ($this->default_locale !== null)
			setlocale(LC_ALL, $this->default_locale);

		parent::run();
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
		$_GET = array();
		$_POST = array();
		parent::replacePage($source);
	}

	// }}}
	// {{{ public function queryForPage()

	public function queryForPage($component)
	{
		$sql = sprintf('select * from getAdminPage(%s, %s, %s)',
            $this->db->quote(true, 'boolean'),
            $this->db->quote($component, 'text'),
            $this->db->quote($this->session->user_id, 'integer'));

		$row = SwatDB::queryRow($this->db, $sql);
		return $row;
	}

	// }}}
	// {{{ public function getDefaultSubComponent()

	/**
	 * Gets the name of the default sub-component of this application
	 *
	 * @return string the name of the default sub-component to use if no
	 *                 sub-component is specified in the page request source.
	 */
	public function getDefaultSubComponent()
	{
		return 'Index';
	}

	// }}}
	// {{{ public function getFrontSource()

	/**
	 * Gets the source of the front page
	 *
	 * @return string the subcomponent page to load as the front page of this
	 *                 admin application.
	 */
	public function getFrontSource()
	{
		return $this->front_source;
	}

	// }}}
	// {{{ public function setFrontSource()

	/**
	 * Sets the source of the front page
	 *
	 * @paramt string $source the subcomponent page to load as the front page
	 *                         of this admin application.
	 */
	public function setFrontSource($source)
	{
		$this->front_source = $source;
	}

	// }}}
	// {{{ protected function normalizeSource()

	protected function normalizeSource($source)
	{
		$source = parent::normalizeSource($source);

		if ($source === 'index.html')
			$source = $this->front_source;

		return $source;
	}

	// }}}
	// {{{ protected function resolvePage()

	protected function resolvePage($source)
	{
		$request = new AdminPageRequest($this, $source);

		$file = $request->getFilename();
		if ($file === null)
			throw new AdminNotFoundException(
				sprintf(Admin::_("File not found for source '%s'."), $source));

		require_once $file;

		$classname = $request->getClassname();
		if (!class_exists($classname))
			throw new AdminNotFoundException(
				sprintf(Admin::_(
					"Class '%s' does not exist in the included file."),
					$classname));

		$layout = $this->resolveLayout($source);
		$page = new $classname($this, $layout);
		$page->title = $request->getTitle();

		if ($page instanceof AdminPage) {
			$page->source = $request->getSource();
			$page->component = $request->getComponent();
			$page->subcomponent = $request->getSubComponent();
		}

		if ($page->layout instanceof AdminLayout) {
			$entry = new AdminImportantNavBarEntry($this->title, '.');
			$page->layout->navbar->addEntry($entry);

			// Don't link the default sub-component navbar entry
			if ($request->getSubComponent() == $this->getDefaultSubComponent())
				$entry = new SwatNavBarEntry($request->getTitle(), null);
			else
				$entry = new SwatNavBarEntry($request->getTitle(),
					$request->getComponent());

			$page->layout->navbar->addEntry($entry);
		}

		return $page;
	}

	// }}}
	// {{{ protected function getDefaultModuleList()

	/**
	 * Gets the list of default modules to load for this applicaiton
	 *
	 * @return array
	 * @see    SiteApplication::getDefaultModuleList()
	 */
	protected function getDefaultModuleList()
	{
		return array(
			'session'  => 'AdminSessionModule',
			'messages' => 'SiteMessagesModule',
			'database' => 'SiteDatabaseModule',
			'config'   => 'SiteConfigModule',
		);
	}

	// }}}
	// {{{ protected function initModules()

	protected function initModules()
	{
		parent::initModules();
		// set up convenience references
		$this->db = $this->database->getConnection();
	}

	// }}}
	// {{{ protected function getSecureSourceList()

	/**
	 * @see SiteApplication::getSecureSourceList()
	 */
	protected function getSecureSourceList()
	{
		$list = parent::getSecureSourceList();
		$list[] = '.*'; // all sources

		return $list;
	}

	// }}}
}

?>
