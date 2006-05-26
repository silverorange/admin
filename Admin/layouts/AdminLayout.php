<?php

/**
 * @package   Admin
 * @copyright 2006 silverorange
 */
require_once 'Swat/SwatForm.php';
require_once 'Swat/SwatFormField.php';
require_once 'Swat/SwatButton.php';
require_once 'Site/layouts/SiteLayout.php';
require_once 'Admin/AdminNavBar.php';
require_once 'Admin/AdminMenuStore.php';
require_once 'Admin/AdminMenuView.php';

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

		$this->data->body_class = '';
		
		$this->initLogoutForm();
		$this->initMenu();

		$this->addHtmlHeadEntries($this->logout_form->getHtmlHeadEntries());
		$this->addHtmlHeadEntries($this->menu->getHtmlHeadEntries());
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
	 * Initializes this application's menu view
	 */
	protected function initMenu()
	{
		if ($this->menu === null) {
			$menu_store = SwatDB::executeStoredProc($this->app->db,
				'getAdminMenu', $this->app->db->quote($_SESSION['user_id'],
				'integer'), 'AdminMenuStore');

			$class = $this->app->menu_class;
			$this->menu = new $class($menu_store);
		}
	}

	// }}}

	// build phase
	// {{{ public function build()

	public function build()
	{
		parent::build();

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
		$this->data->title = $page_title.' - '.$this->app->title;
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
		echo 'Welcome '.$_SESSION['name'].' &nbsp;|&nbsp; ';
		echo '<a href="AdminSite/Profile">Login Settings</a> &nbsp;|&nbsp; ';

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
		$this->layout->body_class =
			($this->menu->isShown()) ? '' : 'hide-menu';

		$this->menu->display();
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
}
?>
