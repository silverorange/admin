<?php

require_once 'Admin/AdminDependencyItem.php';

/**
 * A dependency summary that contains a count of items
 *
 * @package   Admin
 * @copyright 2006 silverorange
 */
class AdminDependencySummary extends AdminDependencyItem
{
	// {{{ public properties

	/**
	 * The number of items in this summary
	 *
	 * @var integer
	 */
	public $count;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new AdminDependencySummary
	 *
	 * This constructor enables the entry to be used in a MDB2 data wrapper
	 * to automatically create objects from a result set.
	 *
	 * @param mixed $data the MDB2 row containing the data for this summary
	 *                     object.
	 */
	public function __construct($data = null)
	{
		if ($data !== null) {
			$this->count = $data->count;
			$this->parent = $data->parent;
			$this->status_level = $data->status_level;
		}
	}

	// }}}

}

?>
