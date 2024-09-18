<?php

/**
 * Admin menu component
 *
 * Internal data class used internally within {@link AdminMenuStore}.
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminMenuComponent
{


	public $id;
	public $shortname;
	public $description;
	public $title;
	public $subcomponents;



	public function __construct($id, $shortname, $title, $description = null)
	{
		$this->id = $id;
		$this->shortname = $shortname;
		$this->title = $title;
		$this->description = $description;
		$this->subcomponents = [];
	}

}

?>
