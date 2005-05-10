<?php

/**
 * Dependency message class
 *
 * This class provides a standard way to display hierachal dependencies.
 * A typical use is for displaying items to be deleted on a confirmation page.
 * The items can be categorized into status levels (eg, DELETE and NODELETE)
 * based upon the existence of dependencies.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminDependency {

	const DELETE = 0;
	const NODELETE = 1;

	/**
	 * Title
	 *
	 * Visible title for the type of entries this dependency object deals with.
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * Status Levels
	 *
	 * Array of possible status levels. These are the categories that items are sorted
	 * into when displayed. The two most common levels, and the default, are "DELETE"
	 * and not "NODELETE". Array keys are integers where a high number gives the
	 * status a higher priority relative to other status levels.  Array elements are 
	 * visible descriptions of the status levels.  The description can optionally
	 * contain a %s placeholder that will be filled with the {AdminDependency::$title}.
	 *
	 * By default two status levels are setup:
	 *        array(AdminDependency::DELETE => _S("The following %s will be deleted:"),
	 *              AdminDependency::NODELETE => _S("The following %s can't be deleted:"))
	 *
	 * @var array
	 */
	public $status_levels = null;

	/**
	 * Entries
	 *
	 * Array of {@link AdminDependencyEntries} to be displayed. Such an array can be 
	 * constructed from database data by calling the static convenience method 
	 * {@link AdminDependendy::queryDependencyEntries()}.
	 *
	 * @var array
	 */
	public $entries = null;

	/**
	 * Default status level
	 *
	 * The default status level to assign to entries that do not already have it set. This value
	 * should correspond to the keys of the {@link AdminDependendy::$status_levels} array.
	 *
	 * @var int
	 */
	public $status_level = AdminDependency::DELETE;

	/**
	 * Display count only
	 *
	 * When true, a one line count of dependent items is displayed rather than a list of 
	 * all items.
	 *
	 * @var boolean
	 */
	public $display_count = false;
	
	private $dependencies = array();

	/**
	 * Get dependency message
	 *
	 * Retrieves the dependency message ready for display. When using a tree of 
	 * AdminDependency objects, this should be called on the root object.
	 *
	 * @returns string HTML structured dependency message.
	 */
	public function getMessage() {

		if ($this->entries === null)
			return '';

		if ($this->status_levels === null) {
			$this->status_levels = array();
			$this->status_levels[AdminDependency::DELETE] = _S("The following %s(s) will be deleted:");
			$this->status_levels[AdminDependency::NODELETE] = _S("The following %s(s) can not be deleted:");
		}

		$this->processDependencies();
		
		ob_start();
		$this->display();

		return ob_get_clean();
	}

	/**
	 * Get count at status level
	 *
	 * Retrieves the number of items at the given status level.
	 *
	 * @param int $status_level The status level to count items in.
	 * @returns int Number of items at $status_level.
	 */
	public function getStatusLevelCount($status_level) {
		$count = 0;
		
		foreach ($this->entries as $entry)
			if ($entry->status_level == $status_level)
				$count++;

		return $count;
	}

	private function processDependencies($parent = null) {
		$ret = 0;
		
		foreach ($this->entries as $entry) {
			if ($entry->status_level === null);
				$entry->status_level = $this->status_level;

			if ($parent === null || $entry->parent == $parent) {
				foreach ($this->dependencies as $dep) {
					$entry->status_level = 
						max($entry->status_level, $dep->processDependencies($entry->id));
				}
				
				$ret = max($ret, $entry->status_level);
			}
		}
		
		return $ret;
	}

	private function display() {
		echo '<div class="admin-dependency">';

		foreach ($this->status_levels as $status_level => $title)
			$this->displayStatusLevel($status_level);

		echo '</div>';
	}

	private function displayStatusLevel($status_level) {
		$first = true;
		
		foreach ($this->entries as $entry) {
			if ($entry->status_level == $status_level) {
				
				if ($first) {
					echo '<h3>';
					printf($this->status_levels[$entry->status_level], $this->title);
					echo '</h3>';
					echo '<ul>';
					$first = false;
				}

				echo '<li>'.$entry->title;
				
				foreach ($this->dependencies as $dep)
					$dep->displayDependencies($entry->id, $status_level);

				echo '</li>';
			}
		}

		if (!$first)
			echo '</ul>';
		
	}
	
	private function displayDependencies($parent, $status_level) {

		if ($this->display_count)
			$this->displayDependencyCount($parent, $status_level);
		else
			$this->displayDependencyList($parent, $status_level);
	}

	private function displayDependencyList($parent, $status_level) {
		$first = true;
	
		foreach ($this->entries as $entry) {
			if ($entry->parent == $parent && $entry->status_level == $status_level) {
				
				if ($first) {
					echo '<br />';
					
					if ($this->title !== null)
						printf(_S("Dependent %s(s):"), $this->title);
					else
						echo _S("Dependent items(s):");
						
					echo '<ul>';
					$first = false;
				}

				echo '<li>'.$entry->title;
				
				foreach ($this->dependencies as $dep)
					$dep->displayDependencies($entry->id, $status_level);

				echo '</li>';
			}
		}

		if (!$first)
			echo '</ul>';
		
	}
	
	private function displayDependencyCount($parent, $status_level) {
		$count = 0;
		
		foreach ($this->entries as $entry)
			if ($entry->parent == $parent && $entry->status_level == $status_level)
				$count++;
		
		if ($count != 0) {
			echo '<ul><li>';
			if ($this->title !== null)
				printf(_S("%d Dependent %s(s)"), $count, $this->title);
			else
				printf(_S("%d Dependent item(s)"), $count);
		}
		
		foreach ($this->entries as $entry)
			if ($entry->parent == $parent && $entry->status_level == $status_level)
				foreach ($this->dependencies as $dep)
					$dep->displayDependencyCount($entry->id, $status_level);
					
		if ($count != 0)
			echo '</ul>';
	}
	
	/**
	 * Add a sub-dependency
	 *
	 * Add another AdminDependency object as a sub-dependency of this one. The parent
	 * fields within the entries of the sub-dependency object should correspond to the
	 * id fields of the entries of this object.
	 *
	 * @param AdminDependency $dep AdminDependency object to add as a sub-dependency.
	 */
	public function addDependency(AdminDependency $dep) {
		$this->dependencies[] = $dep;
	}

	/**
	 * Query for entries.
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
	 * @param string $parent_field The name of the database field to query for 
	 *        the parent, or null. The values in this field should correspond
	 *        to ids in a parent AdminDependency object.  This field can be 
	 *        given in the form type:name where type is a standard MDB2 datatype. 
	 *        If type is ommitted, then integer is assummed for this field.
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
	public static function queryDependencyEntries($db, $table, $id_field, $parent_field, $title_field, 
		$order_by_clause = null, $where_clause = null) {

		$items = SwatDB::getOptionArray($db, $table, $title_field, $id_field, $order_by_clause, $where_clause);
		if ($parent_field === null)
			$parents = null;
		else
			$parents = SwatDB::getOptionArray($db, $table, $parent_field, $id_field, $order_by_clause, $where_clause);
		
		return self::buildDependencyArray($items, $parents);
	}

	/**
	 * Build a dependency array.
	 *
 	 * Convenience method to create an array for {@link AdminDependencyEntry} 
	 * objects. The returned entry array can be directly assigned to the
 	 * {@link AdminDependency::$entries} property.
	 *
	 * @param array $items An associative array in the form of id=>title
	 * @param array $parents An associative array in the form of id=>parent
	 *
	 * @return array An array of {@link AdminDependencyEntries}.
	 */
	public static function buildDependencyArray($items, $parents) {
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
}

class AdminDependencyEntry {
	/**
	 * Unique ID for the entry
	 * @var mixed
	 */
	public $id;

	/**
	 * Title for display
	 * @var string
	 */
	public $title;

	/**
	 * ID of the parent
	 *
	 * Reference to the parent entry in a parent {@link AdminDependency} object, null.
	 *
	 * @var mixed
	 */
	public $parent = null;

	/**
	 * Status level
	 *
	 * Initial status level of this entry (eg, DELETE, NODELETE). Typically it is 
	 * easier to set the initial status level for all entries by setting 
	 * {@link AdminDependency::$status_level}.
	 *
	 * @var int
	 */
	public $status_level = null;
}

?>
