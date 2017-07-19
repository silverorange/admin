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
 * @copyright 2007-2016 silverorange
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
	// {{{ protected function loadComponents()

	/**
	 * Loads the components that this group has access to
	 *
	 * @return AdminComponentWrapper the components this group has access to.
	 */
	protected function loadComponents()
	{
		$sql = sprintf(
			'select AdminComponent.*
			from AdminComponent
				inner join AdminComponentAdmingroupBinding on
					AdminComponentAdminGroupBinding.component =
						AdminComponent.id
			where groupnum = %s',
			$this->db->quote($this->id, 'integer')
		);

		return SwatDB::query($this->db, $sql, 'AdminComponentWrapper');
	}

	// }}}
}

?>
