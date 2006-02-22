<?php

require_once 'Swat/SwatImageLinkCellRenderer.php';
require_once 'Admin/Admin.php';

/**
 * Details Control
 *
 * Convenience class for a Details button
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminDetailsControlCellRenderer extends SwatImageLinkCellRenderer
{
	public function render()
	{
		$this->width  = 22;
		$this->height = 22;
		$this->title = Admin::_('View Details');
		$this->alt = Admin::_('Details');
		$this->image = 'admin/images/admin-generic-document.png';
	
		parent::render();
	}
}

?>
