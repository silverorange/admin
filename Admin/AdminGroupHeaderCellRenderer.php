<?php

require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Admin/Admin.php';

/**
 * Group header cell renderer
 *
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminGroupHeaderCellRenderer extends SwatCellRenderer {

	/**
	 * Header title
	 *
	 * The visible content to display on the header.
	 * @var string
	 */
	public $title = '';

	/**
	 * Change order link href
	 *
	 * When not null, this will produce a link to re-order the items within this group. 
	 * @var string
	 */
	public $order_link = null;

	/**
	 * Change order link value
	 *
	 * Value to substitute into the $order_link.
	 * @var string
	 */
	public $order_value = null;

	public function render($prefix = null) {
	
		echo $this->title;

		if ($this->order_link !== null) {
			$anchor = new SwatHtmlTag('a');
			$anchor->content = Admin::_('Change Order');
			$anchor->href = sprintf($this->order_link, $this->order_value);

			echo ' - ';
			$anchor->display();
		}
	}
}

?>
