<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */

/**
 * The primary navigation in an admin.
 */
class AdminMenu {

	private $sections;

	function __construct($rs) {

		if (MDB2::isError($rs)) 
			throw new Exception($rs->getMessage());

		$this->sections = array();
		$section = null;
		$component = null;

		while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT)) {
			if ($section == null || $row->section != $section->id) {
				$section = new AdminMenuSection($row->section, $row->sectiontitle);
				$this->sections[] = $section;
			}

			if ($component == null || $row->componentid != $component->id) {
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

	public function display() {
		echo '<ul>';

		foreach ($this->sections as $section)
			$section->display();

		echo '</ul>';
	}

}

/**
 * Data class for a menu section.
 */
class AdminMenuSection {

	public $id;
	public $title;
	public $components;

	function __construct($id, $title) {
		$this->id = $id;
		$this->title = $title;
		$this->components = array();
	}

	public function display() {
		echo '<li><span>'.$this->title.'</span>';
		echo '<ul>';

		foreach ($this->components as $component)
			$component->display();

		echo '</ul>';
	}
}

/**
 * Data class for a menu component.
 */
class AdminMenuComponent {

	public $id;
	public $shortname;
	public $title;
	public $subcomponents;

	function __construct($id, $shortname, $title) {
		$this->id = $id;
		$this->shortname = $shortname;
		$this->title = $title;
		$this->subcomponents = array();
	}

	public function display() {
		echo '<li><a href="'.$this->shortname.'">';
		echo $this->title;
		echo '</a>';

		if (count($this->subcomponents)) {
			echo '<ul>';

			foreach ($this->subcomponents as $subcomponent)
				$subcomponent->display();

			echo '</ul>';
		}

		echo '</li>';
	}

}

/**
 * Data class for a menu subcomponent.
 */
class AdminMenuSubcomponent {

	public $shortname;
	public $title;

	function __construct($shortname, $title) {
		$this->shortname = $shortname;
		$this->title = $title;
	}

	public function display() {
		echo '<li><a href="'.$this->shortname.'">';
		echo $this->title;
		echo '</a></li>';
	}
}

