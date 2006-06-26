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
	// {{{ public function __construct()

	public function __construct()
	{
		parent::__construct();
		$this->addStyleSheet('packages/admin/styles/admin-group-link-cell-renderer.css');
		$this->class = 'admin-group-link-cell-renderer';
	}

	// }}}
}

?>
