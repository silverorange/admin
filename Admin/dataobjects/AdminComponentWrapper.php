<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'Admin/dataobjects/AdminComponent.php';

/**
 * A recordset wrapper class for AdminComponent objects
 *
 * @package   Admin
 * @copyright 2007-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       AdminComponent
 */
class AdminComponentWrapper extends SwatDBRecordsetWrapper
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();
		$this->row_wrapper_class = 'AdminComponent';
		$this->index_field = 'id';
	}

	// }}}
}

?>
