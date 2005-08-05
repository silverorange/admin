<?php

/**
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminDependencyEntry
{
	/**
	 * Unique ID for this entry
	 *
	 * @var mixed
	 */
	public $id;

	/**
	 * Title for display
	 *
	 * @var string
	 */
	public $title;

	/**
	 * ID of the parent
	 *
	 * Reference to the parent entry in a parent {@link AdminDependency}
	 * object.
	 *
	 * @var mixed
	 */
	public $parent = null;

	/**
	 * Status level
	 *
	 * Initial status level of this entry (eg, DELETE, NODELETE). Typically it
	 * is easier to set the initial status level for all entries by setting 
	 * {@link AdminDependency::$status_level}.
	 *
	 * @var int
	 */
	public $status_level = null;
}

?>
