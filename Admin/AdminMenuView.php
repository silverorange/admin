<?php

/**
 * Primary navigation menu
 *
 * Displays the primary navigation menu.
 *
 * @package   Admin
 * @copyright 2006 silverorange
 */
class AdminMenuView
{
	protected $store;

	public function __construct($store)
	{
		$this->store = $store;
	}

	/**
	 * Displays this menu
	 *
	 * Outputs the HTML of the menu
	 */
	public function display()
	{
		echo '<ul>';

		foreach ($this->store->sections as $section)
			$this->displaySection($section);

		echo '</ul>';

		$this->displayJavaScript();
	}

	/**
	 * Displays contents of the XHTML head section required by this menu view
	 *
	 * Subclasses should over-ride this method to include custom CSS or
	 * JavaScript.
	 */
	public function displayHtmlHeadEntries()
	{
		echo '<script type="text/javascript" src="admin/javascript/admin-menu.js"></script>';
	}

	public function displaySection($section)
	{
		$section_title_tag = new SwatHtmlTag('a');
		$section_title_tag->class = 'menu-section-title';
		$section_title_tag->href =
			'javascript:menu.toggleSection("admin_menu_section_'.$section->id.'");';

		$section_title_span_tag = new SwatHtmlTag('span');
		$section_title_span_tag->setContent($section->title);

		echo '<li>';
		$section_title_tag->open();
		$section_title_span_tag->display();
		$section_title_tag->close();

		echo '<ul id="admin_menu_section_'.$section->id.'">';

		foreach ($section->components as $component)
			$this->displayComponent($component);

		echo '</ul>';
		echo '</li>';
	}

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

	public function displaySubcomponent($subcomponent, $component_shortname)
	{
		$anchor_tag = new SwatHtmlTag('a');
		$anchor_tag->href = $component_shortname.'/'.$subcomponent->shortname;
		$anchor_tag->setContent($subcomponent->title);

		echo '<li>';
		$anchor_tag->display();
		echo '</li>';
	}

	private function displayJavaScript()
	{
		echo '<script type="text/javascript">'."\n";
		echo 'var menu = new AdminMenu();'."\n";
		echo '</script>';
	}
}

?>
