<?php

require_once 'Swat/SwatImageLinkCellRenderer.php';
require_once 'Admin/Admin.php';

/**
 * Tree Details Control
 *
 * Convenience class for a tree details button
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminTreeControlCellRenderer extends SwatImageLinkCellRenderer
{
	// {{{ public properties

	public $childcount = 0;

	// }}}
	// {{{ public function render()
	
	public function render()
	{
		$this->width  = 22;
		$this->height = 22;

		if ($this->childcount == 0) {
			$this->title = Admin::_('View Details');
			$this->alt = Admin::_('Details');
			$this->image = 'admin/images/admin-generic-document.png';
		} else {
			$this->title = sprintf(Swat::ngettext('View Details (%d sub-item)', 'View Details (%d sub-items)', 
				$this->childcount), $this->childcount);

			$this->alt = Admin::_('Details');
			$this->image = 'admin/images/admin-document-with-contents.png';
		}
	
		parent::render();
	}

	// }}}
}

?>
