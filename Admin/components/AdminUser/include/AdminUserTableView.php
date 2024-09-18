<?php

/**
 * @package   Admin
 * @copyright 2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminUserTableView extends SwatTableView
{


	protected function getRowClasses($row, $count)
	{
		$classes = parent::getRowClasses($row, $count);

		if ($row->is_active) {
			$classes[] = 'active';
		} else {
			$classes[] = 'inactive';
		}

		return $classes;
	}

}

?>
