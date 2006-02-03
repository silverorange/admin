<?php

require_once 'Admin/Admin.php';
require_once 'Admin/AdminDependencyEntry.php';
require_once 'SwatDB/SwatDB.php';
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
 * @copyright 2004-2005 silverorange
 * @see       AdminDBDelete, AdminDependencyEntry
 */
abstract class AdminDependency
{
	// {{{ constants

	/**
	 * Dependency entries at this status level may be deleted
	 */
	const DELETE = 0;

	/**
	 * Dependency entries at this status level can not be deleted
	 */
	const NODELETE = 1;

	// }}}
	// {{{ public properties

	/**
	 * A visible title for the type of entries this dependency object deals
	 * with
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
	 * that dependency entries are sorted into when this dependency is
	 * displayed. The two most common levels -- also the default levels -- are
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

	/**
	 * Array of {@link AdminDependencyEntry} objects to be displayed
	 *
	 * While this is a flat array, the objects in the array contain tree
	 * structure information in their properties.
	 *
	 * Such an array can be automatically constructed from database data by
	 * calling the static convenience method
	 * {@link AdminDependendy::queryDependencyEntries()}.
	 *
	 * @var array
	 * @see AdminDependencyEntry
	 */
	public $entries = null;

	/**
	 * The status level to assign to dependency entries that do not have a
	 * status level assigned. This value should correspond to a value in the
	 * {@link AdminDependendy::$status_levels} array.
	 *
	 * @var integer
	 */
	public $default_status_level = self::DELETE;

	// }}}
	// {{{ private properties

	/**
	 * An array of sub-dependencies of this dependency
	 *
	 * This is an array of {@link AdminDependency} objects that allows a tree
	 * of {@link AdminDependencyEntry} trees to be created.
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
		if ($this->entries === null)
			return '';

		$this->processDependencies();

		ob_start();
		$this->display();
		return ob_get_clean();
	}

	// }}}
	// {{{ public function getStatusLevelCount()

	/**
	 * Gets the number of entries at a given status level
	 *
	 * @param integer $status_level the status level to count entries in.
	 *
	 * @return integer the number of entries at the given status level.
	 */
	public function getStatusLevelCount($status_level)
	{
		$count = 0;
		foreach ($this->entries as $entry)
			if ($entry->status_level == $status_level)
				$count++;

		return $count;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this dependency and all its dependency entries and
	 * sub-dependencies
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
	// {{{ public function addDependency()

	/**
	 * Adds a sub-dependency
	 *
	 * Addis another AdminDependency object as a sub-dependency of this one.
	 * The parent fields within the entries of the sub-dependency object should
	 * correspond to the id fields of the entries of this object.
	 *
	 * @param AdminDependency $dep AdminDependency object to add as a
	 *                              sub-dependency.
	 */
	public function addDependency(AdminDependency $dep)
	{
		$this->dependencies[] = $dep;
	}

	// }}}
	// {{{ public static function queryDependencyEntries()

	/**
	 * Queries for dependency entries
	 *
 	 * Convenience method to query for an array for {@link AdminDependencyEntry}
	 * objects. The returned entry array can be directly assigned to the
 	 * {@link AdminDependency::$entries} property.
	 *
	 * @param MDB2_Driver_Common $db The database connection.
	 *
	 * @param string $table The database table to query.
	 *
	 * @param string $id_field The name of the database field to query for 
	 *        the id. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then integer is 
	 *        assummed for this field.
	 *
	 * @param string $parent_field The name of the database field to query to
	 *        link the child dependencies to the parent, or null. The values
	 *        in this field should correspond to ids in a parent
	 *        AdminDependency object.  This field can be given in the form
	 *        type:name where type is a standard MDB2 datatype. If type is
	 *        ommitted, then integer is assummed for this field.
	 *
	 * @param string $title_field The name of the database field to query for 
	 *        the title. Can be given in the form type:name where type is a
	 *        standard MDB2 datatype. If type is ommitted, then text is 
	 *        assummed for this field.
	 *
	 * @param string $order_by_clause Optional comma deliminated list of 
	 *        database field names to use in the <i>order by</i> clause.
	 *        Do not include "order by" in the string; only include the list
	 *        of field names. Pass null to skip over this paramater.
	 *
	 * @param string $where_clause Optional <i>where</i> clause to limit the 
	 *        returned results.  Do not include "where" in the string; only 
	 *        include the conditionals.
	 *
	 * @return array An array of {@link AdminDependencyEntries}.
	 */
	public static function queryDependencyEntries($db, $table, $id_field,
		$parent_field, $title_field, $order_by_clause = null,
		$where_clause = null)
	{

		$items = SwatDB::getOptionArray($db, $table, $title_field, $id_field,
			$order_by_clause, $where_clause);

		if ($parent_field === null)
			$parents = null;
		else
			$parents = SwatDB::getOptionArray($db, $table, $parent_field,
				$id_field, $order_by_clause, $where_clause);

		return self::buildDependencyArray($items, $parents);
	}

	// }}}
	// {{{ public static function buildDependencyArray()

	/**
	 * Builds a dependency array
	 *
 	 * Convenience method to create a flat array of {@link AdminDependencyEntry}
	 * objects. The returned array of dependency entries may be directly
	 * assigned to the {@link AdminDependency::$entries} property of an
	 * {@link AdminDependency} object.
	 *
	 * @param array $items an associative array of dependent items in the form
	 *                      of id => title. This array is usually constructed
	 *                      from the result of a database query.
	 * @param array $parents an associative array containing tree information
	 *                        for the items array in the form of id = >parent.
	 *                        This array is usually constructed from the result
	 *                        of a database query.
	 *
	 * @return array a flat array of {@link AdminDependencyEntry} objects that
	 *                contains dependency tree information.
	 */
	public static function buildDependencyArray($items, $parents)
	{
		$entries = array();
		foreach ($items as $id => $title) {
			if ($parents === null || array_key_exists($id, $parents)) {

				$entry = new AdminDependencyEntry();
				$entry->id = $id;
				$entry->title = $title;
				$entry->parent = ($parents === null) ? null : $parents[$id];

				$entries[] = $entry;
			}
		}
		return $entries;
	}

	// }}}
	// {{{ public abstract function displayDependencies()

	/**
	 * Displays the dependency entries of this dependency for a given parent
	 * at a given status level
	 * 
	 * @param integer $parent the id of the parent to display the dependency
	 *                         entries for.
	 * @param integer $status_level the status level to display the dependency
	 *                               entries for.
	 */
	public abstract function displayDependencies($parent, $status_level);

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
	 * @param integer $count the number of entries at the given status level.
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
					Admin::ngettext('The following item will be deleted:',
					'The following items will be deleted:', $count);
			} else {
				$message = Admin::ngettext('The following %s will be deleted:',
					'The following %ss will be deleted:', $count);

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
	// {{{ private function processDependencies()

	/**
	 * Figures out the status level of all dependency entries of this
	 * dependency
	 *
	 * If any child elements have a higher priority status than their parents,
	 * the status level of the parent is set to the status level of the
	 * children with the highest priority.
	 *
	 * @param integer $parent the id of the parent entry to process. If the
	 *                         parent id is not specified, all entries are
	 *                         processed.
	 *
	 * @return integer the highest priority status level of the processed
	 *                  entries.
	 */
	private function processDependencies($parent = null)
	{
		$return = 0;
		foreach ($this->entries as $entry) {
			if ($entry->status_level === null)
				$entry->status_level = $this->default_status_level;

			if ($parent === null || $entry->parent == $parent) {
				foreach ($this->dependencies as $dep) {
					$entry->status_level = max($entry->status_level,
						$dep->processDependencies($entry->id));
				}
				$return = max($return, $entry->status_level);
			}
		}
		return $return;
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
