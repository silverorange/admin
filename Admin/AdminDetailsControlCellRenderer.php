<?php

require_once 'Swat/SwatControlCellRenderer.php';
require_once 'Admin/Admin.php';

/**
 * Details Control
 *
 * Convenience class for a Details button
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminDetailsControlCellRenderer extends SwatControlCellRenderer {
	
	public function render() {
		$this->width  = 28;
		$this->height = 22;
		$this->title = Admin::_('View Details');
		$this->alt = Admin::_('Details');
		$this->image = 'admin/images/b_details.png';
	
		parent::render();
	}
}

?>
