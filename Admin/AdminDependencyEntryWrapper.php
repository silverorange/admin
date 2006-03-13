<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'Admin/AdminDependencyEntry.php';

/**
 *
 */
class AdminDependencyEntryWrapper extends SwatDBRecordsetWrapper
{
	public function __construct($rs)
	{
		$this->row_wrapper_class = 'AdminDependencyEntry';
		parent::__construct($rs);
	}
}
