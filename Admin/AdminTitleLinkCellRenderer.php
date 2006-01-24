<?php

require_once 'Swat/SwatLinkCellRenderer.php';

/**
 * A title link cell renderer for Admin index pages
 *
 * Links in the cell renderer are styled as block-level elements, 
 * so other cell renderers in the same table cell may cause layout issues.
 *
 * @package   Admin
 * @copyright 2006 silverorange
 */
class AdminTitleLinkCellRenderer extends SwatLinkCellRenderer
{

	/**
	 * Gets TD-tag attributes
	 *
	 * Overridden here to provide a custom CSS hook for admin title link cells.
	 *
	 * @return array an array of attributes to apply to the TD tag of this cell
	 *                renderer.
	 */
	public function getTdAttributes()
	{
		return array('class' => 'admin-title-link-cell-renderer');
	}
}

?>
