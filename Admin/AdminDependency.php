<?php

require_once 'Admin/Admin.php';
require_once 'Swat/SwatString.php';

/**
 * Dependency message class
 *
 * This class provides a standard way to display hierachal dependencies.
 * The typical use for this class is for displaying items to be deleted on a
 * delete confirmation page.
 *
 * The items can be categorized into status levels (eg, DELETE and NODELETE)
 * based upon the existence of dependencies.
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 * @see       AdminDBDelete, AdminListDependency, AdminSummaryDependency
 */
abstract class AdminDependency
{
	// {{{ constants

	/**
	 * Dependency items at this status level may be deleted
	 */
	const DELETE = 0;

	/**
	 * Dependency items at this status level can not be deleted
	 */
	const NODELETE = 1;

	// }}}
	// {{{ public properties

	/**
	 * A visible title for the type of items this dependency object deals with
	 *
	 * If you are not using a sub-class, attempt to use titles that can be
	 * pluralized by adding a single 's'.
	 *
	 * This title is never required. If you use a sub-class that defines its
	 * own text methods you can choose to ignore this property. If you do not
	 * use this property and do not use a sub-class that defines its own text
	 * methods, generic text is used instead.
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * An array of possible status levels. Status levels are the categories
	 * that dependency items are sorted into when this dependency is displayed.
	 * The two most common levels -- also the default levels -- are
	 * "DELETE" and "NODELETE".
	 *
	 * Status levels are integers where a higher value indicates a higher
	 * priority as compared to other status levels.
	 *
	 * By default two status levels are available:
	 *
	 * <code>
	 * array(
	 *     self::DELETE,
	 *     self::NODELETE
	 * );
	 * </code>
	 *
	 * @var array
	 */
	public $status_levels = null;

	// }}}
	// {{{ private properties

	/**
	 * An array of sub-dependencies of this dependency
	 *
	 * This is an array of {@link AdminDependency} objects that allows a tree
	 * of {@link AdminDependencyItem} objects to be created.
	 *
	 * @var array
	 */
	protected $dependencies = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new dependency object
	 */
	public function __construct()
	{
		$this->status_levels = array(self::DELETE, self::NODELETE);
	}

	// }}}
	// {{{ public function getMessage()

	/**
	 * Gets the dependency message
	 *
	 * Retrieves the dependency message ready for display. When using a tree of 
	 * {@link AdminDependency} objects, this should be called on the
	 * root object.
	 *
	 * @return string the XHTML structured dependency message.
	 */
	public function getMessage()
	{
		if ($this->getItemCount() == 0)
			return '';

		$this->processItemStatuses();

		ob_start();
		$this->display();
		return ob_get_clean();
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this dependency and all its sub-dependencies
	 */
	public function display()
	{
		$dependency_div = new SwatHtmlTag('div');
		$dependency_div->class = 'admin-dependency';
		$dependency_div->open();

		foreach ($this->status_levels as $status_level)
			$this->displayStatusLevel($status_level);

		$dependency_div->close();
	}

	// }}}
	// {{{ public abstract function getStatusLevelCount()

	/**
	 * Gets the number of items in this dependency at a given status level
	 *
	 * @param integer $status_level the status level to count items in.
	 *
	 * @return integer the number of items at the given status level in this
	 *                  dependency.
	 */
	public abstract function getStatusLevelCount($status_level);

	// }}}
	// {{{ public abstract function getItemCount()

	/**
	 * Gets the number of items in this dependency
	 *
	 * @return integer the number of items in this dependency.
	 */
	public abstract function getItemCount();

	// }}}
	// {{{ public abstract function displayDependencies()

	/**
	 * Displays the dependency items of this dependency for a given parent
	 * at a given status level
	 * 
	 * @param integer $parent the id of the parent to display the dependency
	 *                         items for.
	 * @param integer $status_level the status level to display the dependency
	 *                               items for.
	 */
	public abstract function displayDependencies($parent, $status_level);

	// }}}
	// {{{ public abstract function processItemStatuses()

	/**
	 * Figures out the status level of all dependency items of this dependency
	 *
	 * If any child elements have a higher priority status than their parents,
	 * the status level of the parent is set to the status level of the
	 * child with the highest priority.
	 *
	 * @param mixed $parent the id of the parent of the items to process. If
	 *                       the parent id is not specified, all items are
	 *                       processed.
	 *
	 * @return integer the highest priority status level of the processed
	 *                  items.
	 */
	public abstract function processItemStatuses($parent = null);

	// }}}
	// {{{ protected function getStatusLevelText()

	/**
	 * Gets the text representing a status level of this dependency
	 *
	 * Sub-classes may override this method to have more descriptive or
	 * meaningful text. If the text for a non-existant status level in this
	 * dependency is requested an exception is thrown.
	 *
	 * @param integer $status_level the status level to get the textual
	 *                               representation of.
	 * @param integer $count the number of items at the given status level.
	 *
	 * @return string the textual representation of the given status level.
	 *
	 * @thows SwatException
	 */
	protected function getStatusLevelText($status_level, $count)
	{
		switch ($status_level) {
		case self::DELETE:
			if ($this->title === null) {
				$message =
					Admin::ngettext('Delete the following item?',
					'Delete the following items?:', $count);
			} else {
				$message = Admin::ngettext('Delete the following %s?',
					'Delete the following %ss?', $count);

				$message = sprintf($message, $this->title);
			}
			break;

		case self::NODELETE:
			if ($this->title === null) {
				$message =
					Admin::ngettext('The following item can not be deleted:',
					'The following items can not be deleted:', $count);
			} else {
				$message =
					Admin::ngettext('The following %s can not be deleted:',
					'The following %ss can not be deleted:', $count);

				$message = sprintf($message, $this->title);
			}
			break;

		default:
			throw new SwatException('Unknown status level text requested in '.
				'AdminDependency.');
		}
		return $message;
	}

	// }}}
	// {{{ private function displayStatusLevel()

	/**
	 * Displays all the dependency entries at a single status level for this
	 * dependency
	 *
	 * @param integer $status_level the status level to display dependency
	 *                               entries for.
	 */
	private function displayStatusLevel($status_level)
	{
		$count = $this->getStatusLevelCount($status_level);
		$first = true;
		foreach ($this->entries as $entry) {
			if ($entry->status_level == $status_level) {
				if ($first) {
					$header_tag = new SwatHtmlTag('h3');
					$header_tag->setContent(
						$this->getStatusLevelText($status_level, $count));

					$header_tag->display();
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

	// }}}
}

?>
