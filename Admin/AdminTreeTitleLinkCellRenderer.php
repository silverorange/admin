<?php

require_once 'Admin/AdminTitleLinkCellRenderer.php';

/**
 * A title link cell renderer for tree index pages
 *
 * @package   Admin
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminTreeTitleLinkCellRenderer extends AdminTitleLinkCellRenderer
{
	// {{{ public properties

	public $child_count = 0;
	public $base_stock_id = 'document';

	// }}}
	// {{{ protected function setStockType()

	/**
	 * Applies the stock type specificed by the user
	 */
	protected function setStockType()
	{
		if (intval($this->child_count) > 0)
			$this->setFromStock($this->base_stock_id.'-with-contents');
		else
			$this->setFromStock($this->base_stock_id);

		// setting stock_id overrides base_stock_id
		parent::setStockType();
	}

	// }}}
	// {{{ protected function getTitle()

	protected function getTitle()
	{
		if (intval($this->child_count) === 0)
			return Admin::_('no sub-items');

		return sprintf(Admin::ngettext('%d sub-item', '%d sub-items', 
			$this->child_count), $this->child_count);
	}

	// }}}
}

?>
