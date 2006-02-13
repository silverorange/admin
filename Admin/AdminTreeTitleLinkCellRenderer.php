<?php

require_once 'Admin/AdminTreeLinkCellRenderer.php';

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
		if ($this->childcount > 0)
			$this->setFromStock('document-with-sub-documents');
		else
			$this->setFromStock('document');

		parent::render();
	}

	protected function getTitle()
	{
		$this->title = sprintf(Swat::ngettext('View Details (%d sub-item)', 'View Details (%d sub-items)', 
			$this->childcount), $this->childcount);
	}
}

?>
