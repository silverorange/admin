<?php

require_once 'Swat/SwatImageLinkRenderer.php';
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
		$this->width  = 28;
		$this->height = 22;
		$this->title = Admin::_('Edit Item');
		$this->alt = Admin::_('Edit');
		$this->image = 'admin/images/b_edit.png';
	
		parent::render();
	}
}

?>
