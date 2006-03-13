<?php

require_once 'SwatDB/SwatDBRecordsetWrapper.php';
require_once 'Admin/AdminDependencySummary.php';

class AdminDependencySummaryWrapper extends SwatDBRecordsetWrapper
{
	public function __construct($rs)
	{
		$this->row_wrapper_class = 'AdminDependencySummary';
		parent::__construct($rs);
	}
}

?>
