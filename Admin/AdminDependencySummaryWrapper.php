<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'Admin/AdminDependencySummary.php';

/**
 *
 * @package   Admin
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminDependencySummaryWrapper extends SwatDBRecordsetWrapper
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();
		$this->row_wrapper_class = 'AdminDependencySummary';
	}

	// }}}
}

?>
