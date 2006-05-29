<?php

/**
 * Data store for primary navigation menu
 *
 * Designed to be used as a MDB2 result wrapper class.
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 */
class AdminMenuStore
{
	/**
	 * Sections in this menu
	 *
	 * @var AdminMenuSection
	 */
	public $sections;

	/**
	 * @param MDB2_Result $rs A recordset containing the menu.
	 *        Requires the fields: section (integer), section_title (text),
	 *        component_id (integer), shortname (text), title (text)
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
				$section = new AdminMenuSection($row->section,
					$row->section_title);

				$this->sections[$row->section] = $section;
			}

			if ($component === null || $row->component_id != $component->id) {
				$component = new AdminMenuComponent($row->component_id,
					$row->shortname, $row->title);

				$section->components[$row->shortname] = $component;
			}

			if (strlen($row->subcomponent_shortname) != 0) {
				$subcomponent = new AdminMenuSubcomponent(
					$row->subcomponent_shortname, $row->subcomponent_title);

				$component->subcomponents[$row->subcomponent_shortname] =
					$subcomponent;
			}
		}
	}

	public function getComponentByName($name)
	{
		foreach ($this->sections as $section)
			foreach ($section->components as $component)
				if ($component->shortname === $name)
					return $component;

		return null;
	}
}

/**
 * Admin menu section
 *
 * Internal data class used internally within {@link AdminMenuStore}.
 */
class AdminMenuSection
{
	public $id;
	public $title;
	public $components;
	public $show;

	public function __construct($id, $title)
	{
		$this->id = $id;
		$this->title = $title;
		$this->components = array();
		$this->show = true;
	}
}

/**
 * Admin menu component
 *
 * Internal data class used internally within {@link AdminMenuStore}.
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
}

/**
 * Admin menu sub component
 *
 * Internal data class used internally within {@link AdminMenuStore}.
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
}

?>
