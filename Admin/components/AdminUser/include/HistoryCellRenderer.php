<?php

/**
 * Custom cell renderer for the history link on AdminUsers index page
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminUserHistoryCellRenderer extends SwatCellRenderer
{


	public $date;
	public $user;
	public $title;



	public function render()
	{
		if ($this->date !== null) {
			echo ' (';
			$anchor = new SwatHtmlTag('a');
			$anchor->setContent($this->title);
			$anchor->href = sprintf('AdminUser/Details&id=%s', $this->user);
			$anchor->display();
			echo ')';
		}
	}

}

?>
