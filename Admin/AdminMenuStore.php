<?php

/**
 * Data store for primary navigation menu
 *
 * Designed to be used as a MDB2 result wrapper class.
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminMenuStore
{
	// {{{ public properties

	/**
	 * Sections in this menu
	 *
	 * @var AdminMenuSection
	 */
	public $sections;

	// }}}
	// {{{ public function __construct()

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

		do {
			while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT)) {
				if ($section === null || $row->section != $section->id) {
					$section = new AdminMenuSection($row->section,
						$row->section_title);

					$this->sections[$row->section] = $section;
				}

				if ($component === null ||
					$row->component_id != $component->id) {
					$component = new AdminMenuComponent($row->component_id,
						$row->shortname, $row->title, $row->description);

					$section->components[$row->shortname] = $component;
				}

				if ($row->subcomponent_shortname != '') {
					$subcomponent = new AdminMenuSubcomponent(
						$row->subcomponent_shortname, $row->subcomponent_title);

					$component->subcomponents[$row->subcomponent_shortname] =
						$subcomponent;
				}
			}
		} while ($rs->nextResult());
	}

	// }}}
	// {{{ public function getComponentByName()

	public function getComponentByName($name)
	{
		foreach ($this->sections as $section)
			foreach ($section->components as $component)
				if ($component->shortname === $name)
					return $component;

		return null;
	}

	// }}}
}

?>
