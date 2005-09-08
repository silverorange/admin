<?php

/**
 * Primary navigation menu
 *
 * Designed to be used as a MDB2 result wrapper class.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminMenu
{
	private $sections;

	/**
	 * @param MDB2_Result $rs A recordset containing the menu.
	 *        Requires the fields: section (integer), sectiontitle (text),
	 *        componentid (integer), shortname (text), title (text)
	 *        subcomponent_shortname (text), subcomponent_title (text)
	 */
	public function __construct($rs)
	{
		if (MDB2::isError($rs)) 
			throw new Exception($rs->getMessage());

		$this->sections = array();
		$section = null;
		$component = null;

		while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT)) {
			if ($section === null || $row->section != $section->id) {
				$section = new AdminMenuSection($row->section, $row->sectiontitle);
				$this->sections[] = $section;
			}

			if ($component === null || $row->componentid != $component->id) {
				$component = new AdminMenuComponent($row->componentid, 
					$row->shortname, $row->title);
				$section->components[] = $component;
			}

			if (strlen($row->subcomponent_shortname) != 0) {
				$subcomponent = new AdminMenuSubcomponent($row->subcomponent_shortname, 
					$row->subcomponent_title);
				$component->subcomponents[] = $subcomponent;
			}
		}
	}

	/**
	 * Displays this menu
	 *
	 * Outputs the HTML of the menu
	 */
	public function display()
	{
		echo '<ul>';

		foreach ($this->sections as $section)
			$section->display();

		echo '</ul>';
	}
}

/**
 * Admin menu section
 *
 * Internal data/display class used internally within {@link AdminMenu}
 */
class AdminMenuSection
{
	public $id;
	public $title;
	public $components;

	public function __construct($id, $title)
	{
		$this->id = $id;
		$this->title = $title;
		$this->components = array();
	}

	public function display()
	{
		echo '<li><span>'.$this->title.'</span>';
		echo '<ul>';

		foreach ($this->components as $component)
			$component->display();

		echo '</ul>';
	}
}

/**
 * Admin menu component
 *
 * Internal data/display class used internally within {@link AdminMenu}
 */
class AdminMenuComponent
{
	public $id;
	public $shortname;
	public $title;
	public $subcomponents;

	public function __construct($id, $shortname, $title)
	{
		$this->id = $id;
		$this->shortname = $shortname;
		$this->title = $title;
		$this->subcomponents = array();
	}

	public function display()
	{
		echo '<li><a href="'.$this->shortname.'">';
		echo $this->title;
		echo '</a>';

		if (count($this->subcomponents)) {
			echo '<ul>';

			foreach ($this->subcomponents as $subcomponent)
				$subcomponent->display($this->shortname);

			echo '</ul>';
		}

		echo '</li>';
	}
}

/**
 * Admin menu sub component
 *
 * Internal data/display class used internally within {@link AdminMenu}
 */
class AdminMenuSubcomponent
{
	public $shortname;
	public $title;

	public function __construct($shortname, $title)
	{
		$this->shortname = $shortname;
		$this->title = $title;
	}

	public function display($component_shortname)
	{
		echo '<li><a href="'.$component_shortname.'/'.$this->shortname.'">';
		echo $this->title;
		echo '</a></li>';
	}
}

?>
