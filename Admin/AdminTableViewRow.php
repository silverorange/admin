<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatObject.php');

/**
 * Base class for a extra row displayed that the bottom of a SwatTableView.
 */
abstract class AdminTableViewRow extends SwatObject {

	public abstract function display(&$columns);

}
