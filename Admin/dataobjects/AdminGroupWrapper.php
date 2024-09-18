<?php

/**
 * A recordset wrapper class for AdminGroup objects
 *
 * @package   Admin
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       AdminGroup
 */
class AdminGroupWrapper extends SwatDBRecordsetWrapper
{


	protected function init()
	{
		parent::init();
		$this->row_wrapper_class = 'AdminGroup';
		$this->index_field = 'id';
	}

}

?>
