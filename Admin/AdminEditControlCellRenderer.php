<?php

require_once 'Swat/SwatImageLinkCellRenderer.php';
require_once 'Admin/Admin.php';

/**
 * Edit Control
 *
 * Convenience class for an Edit button
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminEditControlCellRenderer extends SwatImageLinkCellRenderer
{
	public function render()
	{
		$this->width  = 22;
		$this->height = 22;
		$this->title = Admin::_('Edit Item');
		$this->alt = Admin::_('Edit');
		$this->image = 'admin/images/admin-edit.png';
	
		parent::render();
	}
}

?>
