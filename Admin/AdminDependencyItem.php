<?php

require_once 'AdminDependency.php';

/**
 * An item in an admin dependency
 *
 * @package   Admin
 * @copyright 2006 silverorange
 * @see       AdminDependencyEntry, AdminDependencySummary
 */
abstract class AdminDependencyItem
{
	/**
	 * Initial status level of this entry (eg, DELETE, NODELETE).
	 *
	 * @var integer
	 *
	 * @see AdminDependency
	 */
	public $status_level = AdminDependency::NODELETE;

	/**
	 * Id of the parent item
	 *
	 * An identifier that references a parent item in a parent
	 * {@link AdminDependency} object.
	 *
	 * @var mixed
	 */
	public $parent = null;
}

?>
