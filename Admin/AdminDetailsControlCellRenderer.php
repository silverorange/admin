<?php
require_once('Swat/SwatControlCellRenderer.php');

/**
 * Details Control
 *
 * Convenience class for a Details button
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminDetailsControlCellRenderer extends SwatControlCellRenderer {
	
	public function render($prefix) {
		$this->width  = 28;
		$this->height = 22;
		$this->title = _("View Details");
		$this->alt = _("Details");
		$this->src = 'admin/images/b_details.png';
	
		parent::render($prefix);
	}
}
