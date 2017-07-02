<?php

/**
 * A recordset wrapper class for AdminSection objects
 *
 * @package   Admin
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       AdminSection
 */
class AdminSectionWrapper extends SwatDBRecordsetWrapper
{
	// {{{ protected function init()

	protected function init()
	{
		parent::init();
		$this->row_wrapper_class = 'AdminSection';
		$this->index_field = 'id';
	}

	// }}}
}

?>
