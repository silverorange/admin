<?php

require_once 'XML/RPCAjax.php';
require_once 'Swat/SwatUIObject.php';
require_once 'Swat/SwatYUI.php';
require_once 'Admin/AdminMenuViewStateStore.php';

/**
 * Displays the primary navigation menu
 *
 * This is the default view. This class may be extended to provide completely
 * different menu styles.
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 */
class AdminMenuView extends SwatUIObject
{
	// {{{ public properties

	/**
	 * The unique identifier of this menu-view
	 *
	 * @var string
	 */
	public $id = '';

	// }}}
	// {{{ protected properties

	/**
	 * The menu-store this menu-view is viewing
	 *
	 * @var AdminMenuStore
	 */
	protected $store;

	/**
	 * The admin application that uses this menu
	 *
	 * @var AdminApplication
	 */
	protected $app;

	// }}}
	// {{{ private properties

	/**
	 * Whether of not this menu-view is shown (not collapsed) or not
	 *
	 * @var boolean
	 */
	private $show;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new menu-view object with a given menu-store and id.
	 *
	 * @param AdminMenuStore $store the menu-store this view will view.
	 * @param string $id optional identifier for this menu-view.
	 */
	public function __construct(AdminMenuStore $store,
		AdminApplication $app, $id = null)
	{
		parent::__construct();

		$this->store = $store;
		$this->app = $app;
		$this->show = true;
		if ($id !== null)
			$this->id = $id;

		$yui = new SwatYUI(array('dom', 'animation'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addJavaScript('packages/admin/javascript/admin-menu.js',
			Admin::PACKAGE_ID);

		$this->addStyleSheet('packages/admin/styles/admin-menu.css',
			Admin::PACKAGE_ID);

		$this->html_head_entry_set->addEntrySet(
			XML_RPCAjax::getHtmlHeadEntrySet());
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this menu-view
	 */
	public function init()
	{
		$this->loadState();
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this menu
	 *
	 * Outputs the HTML of this menu.
	 */
	public function display()
	{
		$this->displayShowLink();

		$menu_div = new SwatHtmlTag('div');
		$menu_div->id = $this->id;
		$menu_div->class = 'admin-menu';
		$menu_div->open();

		$this->displayHideLink();
		$this->displayMenuContent();
		Swat::displayInlineJavaScript($this->getInlineJavaScript());

		$menu_div->close();
	}

	// }}}
	// {{{ public function displaySection()

	/**
	 * Displays a single menu section
	 *
	 * @param AdminMenuSection $section the section to display.
	 */
	public function displaySection($section)
	{
		$section_title_tag = new SwatHtmlTag('a');
		$section_title_tag->class = 'menu-section-title';
		$section_title_tag->href = sprintf(
			'javascript:%s_obj.toggleSection(\'%s\');',
			$this->id, $section->id);

		$section_title_span_tag = new SwatHtmlTag('span');
		$section_title_span_tag->setContent($section->title);

		$section_li_tag = new SwatHtmlTag('li');
		if (!$section->show)
			$section_li_tag->class = 'hide-menu-section';

		$section_li_tag->open();

		$section_title_tag->open();
		$section_title_span_tag->display();
		$section_title_tag->close();

		$section_content = new SwatHtmlTag('ul');
		$section_content->id = sprintf('%s_section_%s', $this->id,
			$section->id);


		$section_content->open();

		foreach ($section->components as $component)
			$this->displayComponent($component);

		$section_content->close();

		$section_li_tag->close();
	}

	// }}}
	// {{{ public function displayComponent()

	/**
	 * Displays a single menu component
	 *
	 * @param AdminMenuComponent $component the component to display.
	 */
	public function displayComponent($component)
	{
		$anchor_tag = new SwatHtmlTag('a');
		$anchor_tag->href = $component->shortname;
		$anchor_tag->setContent($component->title);

		echo '<li>';
		$anchor_tag->display();

		if (count($component->subcomponents)) {
			echo '<ul>';

			foreach ($component->subcomponents as $subcomponent)
				$this->displaySubcomponent($subcomponent, $component->shortname);

			echo '</ul>';
		}

		echo '</li>';
	}

	// }}}
	// {{{ public function displaySubcomponent()

	/**
	 * Displays a single menu subcomponent
	 *
	 * @param AdminmenuSubcomponent $subcomponent the subcomponent to display.
	 * @param string $component_shortname the short name of the component this
	 *                                     subcomponent belongs to.
	 */
	public function displaySubcomponent($subcomponent, $component_shortname)
	{
		$anchor_tag = new SwatHtmlTag('a');
		$anchor_tag->href = $component_shortname.'/'.$subcomponent->shortname;
		$anchor_tag->setContent($subcomponent->title);

		echo '<li>';
		$anchor_tag->display();
		echo '</li>';
	}

	// }}}
	// {{{ public function getState()

	/**
	 * Gets the current state of this menu-view
	 *
	 * @return AdminMenuStateStore the current state of this menu-view.
	 */
	public function getState()
	{
		$state = new AdminMenuViewStateStore($this->id.'_state');
		$state->show = $this->show;
		foreach ($this->store->sections as $section)
			$state->sections_show[$section->id] = $section->show;

		return $state;
	}

	// }}}
	// {{{ public function setState()

	/**
	 * Sets the state of this menu-view to a developer specified state
	 *
	 * @param AdminMenuViewStateStore $state the state to set this menu-view to.
	 */
	public function setState(AdminMenuViewStateStore $state)
	{
		$this->show = $state->show;
		foreach ($this->store->sections as $section)
			if (isset($state->sections_show[$section->id]))
				$section->show = $state->sections_show[$section->id];
	}

	// }}}
	// {{{ public function isShown()

	/**
	 * Whether or not this menu view is shown (not collapsed) or not
	 *
	 * @return boolean whether or not this menu view is shown (not collapsed)
	 *                  or not.
	 */
	public function isShown()
	{
		return $this->show;
	}

	// }}}
	// {{{ public function saveState()

	/**
	 * Save this menu-view's state to the user's session
	 */
	public function saveState()
	{
		$this->getState()->saveToDatabase($this->app->db,
			$this->app->session->getUserId());
	}

	// }}}
	// {{{ protected function displayMenuContent()

	/**
	 * Displays the content of this menu-view
	 */
	protected function displayMenuContent()
	{
		echo '<ul>';

		foreach ($this->store->sections as $section)
			$this->displaySection($section);

		echo '</ul>';
	}

	// }}}
	// {{{ protected function displayShowLink()

	/**
	 * Displays the 'show' link of this menu-view
	 *
	 * The show link shows (expands) a menu-view that has been collapsed.
	 */
	protected function displayShowLink()
	{
		$anchor_tag = new SwatHtmlTag('a');
		$anchor_tag->href = sprintf('javascript:%s_obj.toggle();', $this->id);
		$anchor_tag->id = $this->id.'_show';
		$anchor_tag->class = 'admin-menu-show';
		$anchor_tag->open();

		$img_tag = new SwatHtmlTag('img');
		$img_tag->src = 'packages/admin/images/admin-menu-show.png';
		$img_tag->alt = Admin::_('Show Menu');
		$img_tag->height = 86;
		$img_tag->width = 19;
		$img_tag->display();

		$anchor_tag->close();
	}

	// }}}
	// {{{ protected function displayHideLink()

	/**
	 * Displays the 'hide' link of this menu-view
	 *
	 * The hide link hides (collapses) a menu-view.
	 */
	protected function displayHideLink()
	{
		$anchor_tag = new SwatHtmlTag('a');
		$anchor_tag->href = sprintf('javascript:%s_obj.toggle();', $this->id);
		$anchor_tag->id = $this->id.'_hide';
		$anchor_tag->class = 'admin-menu-hide';
		$anchor_tag->open();

		$img_tag = new SwatHtmlTag('img');
		$img_tag->src = 'packages/admin/images/admin-menu-hide.png';
		$img_tag->alt = Admin::_('Hide Menu');
		$img_tag->height = 20;
		$img_tag->width = 87;
		$img_tag->display();

		$anchor_tag->close();
	}

	// }}}
	// {{{ protected function loadState()

	/**
	 * Loads this menu-view's state from the user's session
	 */
	protected function loadState()
	{
		try {
			$menu_state = AdminMenuViewStateStore::loadFromDatabase(
				$this->app->db, $this->app->session->getUserId());
		} catch (AdminException $e) {
			$this->clearState();
		}

		if ($menu_state !== null)
			$this->setState($menu_state);
	}

	// }}}
	// {{{ protected function clearState()

	/**
	 * Clears this menu-view's state from the user's session
	 */
	protected function clearState()
	{
		$state = new AdminMenuViewStateStore($this->id.'_state');
		$state->show = true;
		foreach ($this->store->sections as $section)
			$state->sections_show[$section->id] = true;

		$this->setState($state);
		$this->saveState();
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript required for this menu-view to function
	 *
	 * @return string the inline JavaScript required for this menu-view to
	 *                 function.
	 */
	protected function getInlineJavaScript()
	{
		return sprintf("var %s_obj = new AdminMenu('%s');",
			$this->id, $this->id);
	}

	// }}}
}

?>
