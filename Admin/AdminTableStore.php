<?php

require_once('Swat/SwatTableStore.php');

/**
 * AdminTableStore
 *
 * A subclass of SwatTableStore that can be used as an MDB2 results wrappper.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminTableStore extends SwatTableStore {

	function __construct($rs) {
		parent::__construct();

		if (MDB2::isError($rs)) 
			throw new Exception($rs->getMessage());

		while ($row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT))
			$this->addRow($row);
	}
}
