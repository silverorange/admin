<?php

require_once 'Admin/AdminTitleLinkCellRenderer.php';

/**
 * A title link cell renderer for tree index pages
 *
 * @package   Admin
 * @copyright 2006 silverorange
 */
class AdminTreeTitleLinkCellRenderer extends AdminTitleLinkCellRenderer
{
	public $childcount = 0;

	public function render()
	{
		if ($this->stock_id === null) {
			if ($this->childcount > 0)
				$this->stock_id = 'document-with-contents';
			else
				$this->stock_id = 'document';
		}

		parent::render();
	}

	protected function getTitle()
	{
		$this->title = sprintf(Swat::ngettext('View Details (%d sub-item)', 'View Details (%d sub-items)', 
			$this->childcount), $this->childcount);
	}
}

?>
