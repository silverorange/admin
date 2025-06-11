<?php

/**
 * A navagational bar for the admin
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
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
	 * @param boolean $first whether or not this entry should be displayed as
	 *                        the first entry.
	 *
	 * @see SwatNavBar::displayEntry()
	 */
	protected function displayEntry(
		SwatNavBarEntry $entry,
		$link = true,
		$first = false
	) {
		if ($entry instanceof AdminImportantNavBarEntry &&
			$entry->link !== null && $link) {

			echo '<h1>';
			$a_tag = new SwatHtmlTag('a');
			if ($first)
				$a_tag->class = 'swat-navbar-first';

			$a_tag->href = $entry->link;
			$a_tag->setContent($entry->title);
			$a_tag->display();
			echo '</h1>';

		} else {
			parent::displayEntry($entry, $link, $first);
		}
	}


}

?>
