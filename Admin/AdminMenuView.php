<?php

/**
 * Primary navigation menu
 *
 * Displays the primary navigation menu.
 *
 * @package Admin
 * @copyright silverorange 2006
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
	}

	/**
	 * Displays contents of the XHTML head section required by this menu view
	 *
	 * Subclasses should over-ride this method to include custom CSS or
	 * JavaScript.
	 */
	public function displayHtmlHeadEntries()
	{
	}

	public function displaySection($section)
	{
		$span_tag = new SwatHtmlTag('span');
		$span_tag->setContent($section->title);

		echo '<li>';
		$span_tag->display();
		echo '<ul>';

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
}

?>
