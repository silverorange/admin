<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'Admin/dataobjects/AdminUserHistory.php';

/**
 * A recordset wrapper class for AdminUserHistory objects
 *
 * @package   Admin
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       AdminUserHistory
 */
class AdminUserHistoryWrapper extends SwatDBRecordsetWrapper
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();
		$this->row_wrapper_class = 'AdminUserHistory';
		$this->index_field = 'id';
	}

	// }}}
}

?>
