<?php

require_once 'Swat/SwatNavBar.php';
require_once 'Admin/AdminImportantNavBarEntry.php';

/**
 * A navagational bar for the admin
 *
 * @package   Admin
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatNavBar
 */
class AdminNavBar extends SwatNavBar
{
	/**
	 * Displays an entry in this navigational bar
	 *
	 * @param SwatNavBarEntry $entry the entry to display.
	 * @param boolean $link whether or not to hyperlink the given entry if the
	 *                       entry has a link set.
	 *
	 * @see SwatNavBar::displayEntry()
	 */
	protected function displayEntry(SwatNavBarEntry $entry, $link = true)
	{
		if ($entry instanceof AdminImportantNavBarEntry) {
			if ($entry->link !== null && $link) {
				echo '<h1>';
				$link_tag = new SwatHtmlTag('a');
				$link_tag->href = $entry->link;
				$link_tag->content = $entry->title;
				$link_tag->display();
				echo '</h1>';
			} else {
				echo $entry->title;
			}
		} else {
			parent::displayEntry($entry, $link);
		}
	}
}

?>
