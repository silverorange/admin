<?php

require_once 'Swat/SwatString.php';
require_once 'Swat/SwatForm.php';
require_once 'Swat/SwatFormField.php';
require_once 'Swat/SwatButton.php';
require_once 'Site/layouts/SiteLayout.php';
require_once 'Admin/AdminNavBar.php';
require_once 'Admin/AdminMenuStore.php';
require_once 'Admin/AdminMenuView.php';

/**
 * @package   Admin
 * @copyright 2006 silverorange
 */
class AdminLayout extends SiteLayout
{
	// {{{ public properties

	/**
	 * @var SwatNavBar Navigation bar.
	 */
	public $navbar;

	/**
	 * The logout form for this page
	 *
	 * This form is responsible for displaying the admin logout button.
	 *
	 * @var SwatForm
	 */
	public $logout_form = null;

	/**
	 * This application's menu view
	 *
	 * @var AdminMenuView
	 */
	public $menu = null;

	// }}}
	// {{{ public function __construct()

	public function __construct($app, $filename = null)
	{
		parent::__construct($app, $filename);
		$this->navbar = new AdminNavBar();
	}

	// }}}

	// init phase
	// {{{ public function init()

	public function init()
	{
		parent::init();

		$this->data->body_classes = new ArrayObject();
		
		$this->initLogoutForm();
		$this->initMenu();
	}

	// }}}
	// {{{ protected function initLogoutForm()

	protected function initLogoutForm()
	{

		$this->logout_form = new SwatForm('logout');
		$this->logout_form->action = 'AdminSite/Logout';

		$form_field = new SwatFormField('logout_button_container');

		$button = new SwatButton('logout_button');
		$button->title = Admin::_('Logout');

		$form_field->add($button);
		$this->logout_form->add($form_field);
	}

	// }}}
	// {{{ protected function initMenu()

	/**
	 * Initializes layout menu view
	 */
	protected function initMenu()
	{
		if ($this->menu === null) {
			$menu_store = SwatDB::executeStoredProc($this->app->db,
				'getAdminMenu',
				$this->app->db->quote($this->app->session->getUserId(),
					'integer'),
				'AdminMenuStore');

			$class = $this->app->getMenuViewClass();
			$this->menu = new $class($menu_store, $this->app);
		}

		$this->menu->init();
	}

	// }}}

	// build phase
	// {{{ public function build()

	public function build()
	{
		parent::build();

		$this->addHtmlHeadEntry(new SwatStyleSheetHtmlHeadEntry(
			'packages/admin/styles/admin.css', Admin::PACKAGE_ID));

		$this->addHtmlHeadEntrySet($this->logout_form->getHtmlHeadEntrySet());
		$this->addHtmlHeadEntrySet($this->menu->getHtmlHeadEntrySet());
	}

	// }}}
	// {{{ protected function buildLogoutForm()

	protected function buildLogoutForm()
	{
		$this->logout_form = new SwatForm('logout');
		$this->logout_form->action = 'AdminSite/Logout';

		$form_field = new SwatFormField('logout_button_container');

		$button = new SwatButton('logout_button');
		$button->title = Admin::_('Logout');

		$form_field->add($button);

		$this->logout_form->add($form_field);
	}

	// }}}

	// finalize phase
	// {{{ public function finalize()

	public function finalize()
	{
		parent::finalize();

		$this->startCapture('navbar');
		$this->navbar->display();
		$this->endCapture();

		$this->startCapture('header');
		$this->displayHeader();
		$this->endCapture();

		$this->startCapture('menu');
		$this->displayMenu();
		$this->endCapture();

		$page_title = $this->navbar->getLastEntry()->title;
		$this->data->title = SwatString::minimizeEntities($page_title).
			' - '.SwatString::minimizeEntities($this->app->title);
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
		echo '<div id="admin-syslinks">',
			'Welcome ',
			SwatString::minimizeEntities($this->app->session->getName()),
			' &nbsp;|&nbsp; ',
			'<a href="AdminSite/Profile">Login Settings</a> &nbsp;|&nbsp; ';

		$this->logout_form->display();

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
		if (!$this->menu->isShown())
			$this->data->body_classes[] = 'hide-menu';

		$this->menu->display();
	}

	// }}}
}

?>
