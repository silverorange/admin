<?php

require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatYUI.php';
require_once 'Site/SiteCommentFilter.php';
require_once 'Admin/AdminMenuStore.php';

/**
 * Displays the primary navigation menu
 *
 * This is the default view. This class may be extended to provide completely
 * different menu styles.
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminMenuView extends SwatControl
{
	// {{{ public properties

	/**
	 * The unique identifier of this menu-view
	 *
	 * If not set explicitly, an id will be auto-generated after this menu-view
	 * is initialized.
	 *
	 * @var string
	 */
	public $id;

	// }}}
	// {{{ protected properties

	/**
	 * The menu-store this menu-view is viewing
	 *
	 * @var AdminMenuStore
	 */
	protected $store;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new menu-view control
	 *
	 * @param string $id optional identifier for this menu-view.
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$yui = new SwatYUI(array('dom', 'event', 'animation'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addStyleSheet('packages/admin/styles/admin-menu.css');
		$this->addJavaScript('packages/admin/javascript/admin-menu.js');
	}

	// }}}
	// {{{ public function setModel()

	/**
	 * @param AdminMenuStore $store the menu-store this view will view.
	 */
	public function setModel(AdminMenuStore $store)
	{
		$this->store = $store;
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
		if (!$this->visible) {
			return;
		}

		parent::display();

		$menu_div = new SwatHtmlTag('div');
		$menu_div->id = $this->id;
		$menu_div->class = 'admin-menu';
		$menu_div->open();

		$this->displayMenuContent();

		$menu_div->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
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
		$section_title_tag = new SwatHtmlTag('span');
		$section_title_tag->class = 'menu-section-title';

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
		echo '<li class="admin-menu-component">';

		$anchor_tag = new SwatHtmlTag('a');
		$anchor_tag->href = $component->shortname;
		$anchor_tag->setContent($component->title);
		$anchor_tag->display();

		if ($component->description != '') {
			echo '<span class="admin-menu-help">';
			echo '<span class="admin-menu-help-arrow"></span>';

			$span_tag = new SwatHtmlTag('span');
			$span_tag->class = 'admin-menu-help-content';
			$span_tag->setContent(
				SiteCommentFilter::toXhtml($component->description),
				'text/xml'
			);
			$span_tag->display();

			echo '</span>';
		}

		$span_tag = new SwatHtmlTag('span');
		$span_tag->class = 'admin-menu-help';

		if (count($component->subcomponents)) {
			echo '<ul>';

			foreach ($component->subcomponents as $subcomponent) {
				$this->displaySubcomponent($subcomponent, $component->shortname);
			}

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
	// {{{ protected function displayMenuContent()

	/**
	 * Displays the content of this menu-view
	 */
	protected function displayMenuContent()
	{
		echo '<ul>';

		foreach ($this->store->sections as $section) {
			$this->displaySection($section);
		}

		echo '</ul>';
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	protected function getInlineJavaScript()
	{
		return sprintf(
			"%s_obj = new AdminMenu(%s);\n",
			$this->id,
			SwatString::quoteJavaScriptString($this->id)
		);
	}

	// }}}
}

?>
