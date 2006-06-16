<?php

require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * Custom cell renderer for the history link on AdminUsers index page
 *
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminUsersHistoryCellRenderer extends SwatCellRenderer
{
	// {{{ public properties

	public $date;
	public $user;
	public $title;

	// }}}
	// {{{ public function render()

	public function render()
	{
		if ($this->date !== null) {
			echo ' (';
			$anchor = new SwatHtmlTag('a');
			$anchor->setContent($this->title);
			$anchor->href = sprintf('AdminUsers/Details&id=%s', $this->user);
			$anchor->display();
			echo ')';
		}
	}

	// }}}
}

?>
