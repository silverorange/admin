<?php

/**
 * Default layout used for the majority of admin components.
 *
 * Includes navigation menu and lagout form.
 *
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminDefaultLayout extends AdminLayout
{
    /**
     * Breadcrumb navigation.
     *
     * @var SwatNavBar
     */
    public $navbar;

    /**
     * The logout form for this page.
     *
     * This form is responsible for displaying the admin logout button.
     *
     * @var SwatForm
     */
    public $logout_form;

    /**
     * This admin application's menu view.
     *
     * @var AdminMenuView
     */
    public $menu;

    public function __construct($app, $filename = null)
    {
        parent::__construct($app, $filename);
        $this->navbar = new AdminNavBar();
        $this->navbar->separator = ' › ';
    }

    // init phase

    public function init()
    {
        parent::init();

        $this->initLogoutForm();
        $this->initMenu();
    }

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

    /**
     * Initializes layout menu view.
     */
    protected function initMenu()
    {
        if ($this->menu === null) {
            $menu_store = SwatDB::executeStoredProc(
                $this->app->db,
                'getAdminMenu',
                $this->app->db->quote(
                    $this->app->session->getUserId(),
                    'integer'
                ),
                'AdminMenuStore'
            );

            $class = $this->app->getMenuViewClass();
            $this->menu = new $class();
            $this->menu->setModel($menu_store);
        }

        $this->menu->init();
    }

    // finalize phase

    public function finalize()
    {
        parent::finalize();

        $this->startCapture('navbar');
        $this->displayNavBar();
        $this->endCapture();

        $this->startCapture('header');
        $this->displayHeader();
        $this->endCapture();

        $this->startCapture('menu');
        $this->displayMenu();
        $this->endCapture();

        $page_title = $this->navbar->getLastEntry()->title;
        $this->data->title = SwatString::minimizeEntities($page_title) .
            ' - ' . SwatString::minimizeEntities($this->app->title);

        $this->addHtmlHeadEntrySet($this->navbar->getHtmlHeadEntrySet());
        $this->addHtmlHeadEntrySet($this->logout_form->getHtmlHeadEntrySet());
        $this->addHtmlHeadEntrySet($this->menu->getHtmlHeadEntrySet());
    }

    /**
     * Display admin page header.
     *
     * Display common elements for the header of an admin page. Sub-classes
     * should call this from their implementation of {@link AdminPage::display()}.
     */
    protected function displayHeader()
    {
        echo '<div id="admin-syslinks">',
        '<span id="admin-identifier">Welcome ',
        SwatString::minimizeEntities($this->app->session->getName()),
        ' &nbsp; ',
        '<a href="AdminSite/Profile">Login Settings</a> &nbsp; </span>';

        $this->logout_form->display();

        echo '</div>';
    }

    protected function displayNavBar()
    {
        $this->navbar->display();
    }

    /**
     * Display admin page menu.
     *
     * Display the menu of an admin page. Sub-classes should call this
     * from their implementation of {@link AdminPage::display()}.
     */
    protected function displayMenu()
    {
        $this->menu->display();
    }
}
