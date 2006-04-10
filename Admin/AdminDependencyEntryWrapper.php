<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'Admin/AdminDependencyEntry.php';

/**
 *
 * @package   Admin
 * @copyright 2006 silverorange
 */
class AdminDependencyEntryWrapper extends SwatDBRecordsetWrapper
{
	protected function init()
	{
		parent::init();
		$this->row_wrapper_class = 'AdminDependencyEntry';
	}
}
