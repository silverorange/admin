<?php

require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatString.php';
require_once 'Admin/AdminDependency.php';

/**
 * A dependency that displays its dependencies as a one line summary
 *
 * @package   Admin
 * @copyright 2006 silverorange
 */
class AdminSummaryDependency extends AdminDependency
{
	/**
	 * Gets the text for a dependency list summary for this dependency
	 *
	 * Sub-classes may override this method to have more descriptive or
	 * meaningful text.
	 *
	 * @param integer $count the number of dependencies in the list.
	 *
	 * @return string the text for a dependency list summary for this
	 *                 dependency.
	 */
	protected function getDependencyText($count)
	{
		if ($this->title === null) {
			$message = Admin::ngettext('%s dependent item',
				'%s dependent items', $count);

			$message = sprintf($message, SwatString::numberFormat($count));
		} else {
			$message = Admin::ngettext('%s dependent %s',
				'%s dependent %ss', $count);

			$message = sprintf($message, SwatString::numberFormat($count),
				$this->title);
		}
		return $message;
	}

	/**
	 * Displays a summary of the dependency entries for the given parent
	 * at a given status level
	 *
	 * @param integer $parent the id of the parent to display the summary for.
	 * @param integer $status_level the status level to display the summary for.
	 */
	public function displayDependencies($parent, $status_level)
	{
		$count = 0;
		foreach ($this->entries as $entry)
			if ($entry->parent == $parent &&
				$entry->status_level == $status_level)
				$count++;

		if ($count != 0) {
			$span_tag = new SwatHtmlTag('span');
			$span_tag->class = 'admin-light';
			$span_tag->setContent($this->getDependencyText($count));

			$span_tag->open();
			echo ' (';
			$span_tag->displayContent();
		}

		foreach ($this->entries as $entry)
			if ($entry->parent == $parent &&
				$entry->status_level == $status_level)
				foreach ($this->dependencies as $dep)
					$dep->displayDependencies($entry->id, $status_level);

		if ($count != 0) {
			echo ')';
			$span_tag->close();
		}
	}
}

?>
