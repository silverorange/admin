<?php

require_once 'Swat/SwatControlCellRenderer.php';
require_once 'Admin/Admin.php';

/**
 * Tree Details Control
 *
 * Convenience class for a tree details button
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminTreeControlCellRenderer extends SwatControlCellRenderer {
	
	public $childcount = 0;
	
	public function render() {
		$this->width  = 28;
		$this->height = 22;

		if ($this->childcount == 0) {
			$this->title = Admin::_('View Details');
			$this->alt = Admin::_('Details');
			$this->image = 'admin/images/b_details.png';
		} else {
			$this->title = sprintf(Swat::ngettext('View Details (%d sub-item)', 'View Details (%d sub-items)', 
				$this->childcount), $this->childcount);

			$this->alt = Admin::_('Details');
			$this->image = 'admin/images/b_details_folder.png';
		}
	
		parent::render();
	}
}

?>
