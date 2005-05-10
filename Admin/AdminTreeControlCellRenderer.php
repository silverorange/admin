<?php

require_once('Swat/SwatControlCellRenderer.php');

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
	
	public function render($prefix) {
		$this->width  = 28;
		$this->height = 22;

		if ($this->childcount == 0) {
			$this->title = _S('View Details');
			$this->alt = _S('Details');
			$this->src = 'admin/images/b_details.png';
		} else {
			$this->title = sprintf(_nS('View Details (%d sub-item)', 'View Details (%d sub-items)', 
				$this->childcount), $this->childcount);

			$this->alt = _S("Details");
			$this->src = 'admin/images/b_details_folder.png';
		}
	
		parent::render($prefix);
	}
}

?>
