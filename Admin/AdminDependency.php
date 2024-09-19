<?php

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
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
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
	// {{{ protected properties

	/**
	 * A visible title for the type of items this dependency object deals with
	 * (singular form)
	 *
	 * @var string
	 *
	 * @see AdminDependency::setTitle()
	 */
	public $singular_title = null;

	/**
	 * A visible title for the type of items this dependency object deals with
	 * (plural form)
	 *
	 * @var string
	 *
	 * @see AdminDependency::setTitle()
	 */
	public $plural_title = null;

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
	// {{{ public function setTitle()

	/**
	 * Sets the user-visible title of the type of items this dependency object
	 * deals with
	 *
	 * Both the singular and plural forms are required when setting the title.
	 * Titles should be all lowercase, not title-case.
	 *
	 * @param string $singular a visible title for the type of items this
	 *                          dependency object deals with (singular form).
	 * @param string $plural a visible title for the type of items this
	 *                          dependency object deals with (plural form).
	 */
	public function setTitle($singular, $plural)
	{
		$this->singular_title = $singular;
		$this->plural_title = $plural;
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
		foreach ($this->status_levels as $status_level)
			$this->displayStatusLevel($status_level);
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
			$title = $this->getTitle($count);
			$message = Admin::ngettext(
				'Delete the following %s?',
				'Delete the following %s?', $count);

			$message = sprintf($message, $title);
			break;

		case self::NODELETE:
			$title = $this->getTitle($count);
			$message = Admin::ngettext(
				'The following %s can not be deleted:',
				'The following %s can not be deleted:', $count);

			$message = sprintf($message, $title);
			break;

		default:
			throw new SwatException('Unknown status level text requested in '.
				'AdminDependency.');
		}
		return $message;
	}

	// }}}
	// {{{ protected function getTitle()

	/**
	 * Helper method to get an appropriate title for the type of item this
	 * dependency object deal with
	 *
	 * This is set with {@link AdminDependency::setTitle()}. If the title is
	 * not set on this object, a generic text of 'item' or 'items' is returned.
	 *
	 * @param integer $count the number of items to get the title for. This
	 *                        determines whether the singular or plural form is
	 *                        returned.
	 *
	 * @return string an appropriate title for the type of item this dependency
	 *                 object deals with.
	 */
	protected function getTitle($count)
	{
		// Note: ngettext() is intentionally used here instead of
		// Admin::ngettext(). Developers are responsible for translating the
		// values passed to setTitle().
		if ($this->singular_title === null || $this->plural_title === null) {
			return ngettext('item', 'items', $count);
		} else {
			return ngettext($this->singular_title, $this->plural_title, $count);
		}
	}

	// }}}
	// {{{ protected function displayStatusLevel()

	/**
	 * Displays all the dependency entries at a single status level for this
	 * dependency
	 *
	 * @param integer $status_level the status level to display dependency
	 *                               entries for.
	 */
	abstract protected function displayStatusLevel($status_level);

	// }}}
	// {{{ protected function displayStatusLevelHeader()

	protected function displayStatusLevelHeader($status_level, $count)
	{
		$header_tag = new SwatHtmlTag('h3');
		$header_tag->setContent(
			$this->getStatusLevelText($status_level, $count));

		$header_tag->display();
	}

	// }}}
}

?>
