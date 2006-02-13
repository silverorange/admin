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
	public $base_stock_id = 'document';

	public function render()
	{
		$this->setStockType();
		parent::render();
	}

	protected function setStockType()
	{
		if (intval($this->childcount) > 0)
			$this->setFromStock($this->base_stock_id.'-with-contents');
		else
			$this->setFromStock($this->base_stock_id);
	}

	protected function getTitle()
	{
		if (intval($this->childcount) == 0)
			return Admin::gettext('no sub-items');

		return sprintf(Admin::ngettext('%d sub-item', '%d sub-items', 
			$this->childcount), $this->childcount);
	}
}

?>
