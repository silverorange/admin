<?php

require_once 'Admin/AdminDependency.php';
require_once 'Swat/SwatString.php';

/**
 * A dependency that displays its dependencies as a list
 *
 * @package   Admin
 * @copyright 2006 silverorange
 */
class AdminListDependency extends AdminDependency
{
	/**
	 * Gets the text for a dependency list for this dependency
	 *
	 * Sub-classes may override this method to have more descriptive or
	 * meaningful text.
	 *
	 * @param integer $count the number of dependencies in the list.
	 *
	 * @return string the text for a dependency list for this dependency.
	 */
	protected function getDependencyText($count)
	{
		if ($this->title === null) {
			$message = Admin::ngettext('Dependent item:',
				'Dependent items:', $count);
		} else {
			$message = Admin::ngettext('Dependent %s:',
				'Dependent %ss:', $count);

			$message = sprintf($message, $this->title);
		}
		return $message;
	}

	/**
	 * Displays a list of the dependency entries of this dependency for a given
	 * parent at a given status level
	 *
	 * @param integer $parent the id of the parent to display the list for.
	 * @param integer $status_level the status level to display the list for.
	 */
	public function displayDependencies($parent, $status_level)
	{
		$count = 0;
		foreach ($this->entries as $entry)
			if ($entry->parent == $parent &&
				$entry->status_level == $status_level)
				$count++;

		$first = true;

		foreach ($this->entries as $entry) {
			if ($entry->parent == $parent &&
				$entry->status_level == $status_level) {

				if ($first) {
					echo '<br />';
					echo SwatString::minimizeEntities($this->getDependencyText($count));
					echo '<ul>';
					$first = false;
				}

				echo '<li>';
				echo SwatString::minimizeEntities($entry->title);

				foreach ($this->dependencies as $dep)
					$dep->displayDependencies($entry->id, $status_level);

				echo '</li>';
			}
		}

		if ($count > 0)
			echo '</ul>';
	}
}

?>
