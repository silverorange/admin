<?php
require_once('Swat/SwatCellRenderer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * Group header cell renderer
 *
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminCellRendererGroupHeader extends SwatCellRenderer {

	/**
	 * Cell content
	 *
	 * The content to place within the cell. In a SwatUI XML file 
	 * this comes from the content of the SwatCellRendererGroupHeader tag.
	 * @var string
	 */
	public $content = '';

	/**
	 * Change order link href
	 *
	 * When not null, this will produce a link to re-order the items within this group. 
	 * @var string
	 */
	public $order_href = null;

	/**
	 * Change order link value
	 *
	 * Value to substitute into the $link_href.
	 * @var string
	 */
	public $order_value = null;

	public function render($prefix) {
	
		echo $this->content;

		if ($this->order_href !== null) {
			$anchor = new SwatHtmlTag('a');
			$anchor->content = _S("Change Order");
			$anchor->href = sprintf($this->order_href, $this->order_value);

			echo ' - ';
			$anchor->display();
		}
	}
}
