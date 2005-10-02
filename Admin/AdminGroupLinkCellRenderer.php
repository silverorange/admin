<?php

require_once 'Swat/SwatLinkCellRenderer.php';

/**
 * A link cell renderer to display in group headers
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminGroupLinkCellRenderer extends SwatLinkCellRenderer
{
	public function __construct()
	{
		$this->class = 'admin-group-link-cell-renderer';
	}
}

?>
