<?php

require_once 'SwatDB/SwatDBDataObject.php';

/**
 * Group of admin users
 *
 * Groups are used to oranize users in the Admin package. Groups are assigned
 * a set of component access rights. Users are assigned to groups. In this way,
 * users receive access to only certain components.
 *
 * @package   Admin
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminGroup extends SwatDBDataObject
{
	// {{{ public properties

	/**
	 * Unique identifier
	 *
	 * @var integer
	 */
	public $id;

	/**
	 * Title of this group 
	 *
	 * @var string
	 */
	public $title;

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'AdminGroup';
		$this->id_field = 'integer:id';
	}

	// }}}
}

?>
