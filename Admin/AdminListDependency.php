<?php

/**
 * A dependency that displays a list of entries.
 *
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminListDependency extends AdminDependency
{
    /**
     * Array of {@link AdminDependencyEntry} objects to be displayed.
     *
     * While this is a flat array, the objects in the array contain tree
     * structure information in their properties.
     *
     * Such an array may be automatically constructed from database data by
     * calling the static convenience method
     * {@link AdminListDependency::queryEntries()}.
     *
     * @var array
     *
     * @see AdminDependencyEntry, AdminDependencyEntryWrapper
     */
    public $entries = [];

    /**
     * Adds a sub-dependency.
     *
     * Adds another AdminDependency object as a sub-dependency of this one.
     * The parent fields within the items of the sub-dependency object should
     * correspond to the id fields of the entries of this dependency.
     *
     * @param AdminDependency $dep the sub-dependency to add
     */
    public function addDependency(AdminDependency $dep)
    {
        $this->dependencies[] = $dep;
    }

    /**
     * Gets the number of entries in this dependency at a given status level.
     *
     * @param int $status_level the status level to count entries in
     *
     * @return int the number of entries at the given status level in this
     *             dependency
     */
    public function getStatusLevelCount($status_level)
    {
        $count = 0;
        foreach ($this->entries as &$entry) {
            if ($entry->status_level === $status_level) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Gets the number of entries in this dependency.
     *
     * @return int the number of entries in this dependency
     */
    public function getItemCount()
    {
        return count($this->entries);
    }

    /**
     * Processes the status level of entries in this dependency.
     *
     * @param $parent mixed the parent identifier of entries to process
     *
     * @see AdminDependency::processItemStatuses()
     */
    public function processItemStatuses($parent = null)
    {
        $return = self::DELETE;

        foreach ($this->entries as $entry) {
            if ($parent === null || $entry->parent === $parent) {
                foreach ($this->dependencies as $dep) {
                    $entry->status_level = max(
                        $entry->status_level,
                        $dep->processItemStatuses($entry->id)
                    );
                }
                $return = max($return, $entry->status_level);
            }
        }

        return $return;
    }

    /**
     * Displays a list of the dependency entries of this dependency for a given
     * parent at a given status level.
     *
     * @param int $parent       the id of the parent to display the list for
     * @param int $status_level the status level to display the list for
     */
    public function displayDependencies($parent, $status_level)
    {
        $count = 0;
        foreach ($this->entries as $entry) {
            if ($entry->parent == $parent
                && $entry->status_level == $status_level) {
                $count++;
            }
        }

        $first = true;

        foreach ($this->entries as $entry) {
            if ($entry->parent == $parent
                && $entry->status_level == $status_level) {
                if ($first) {
                    echo '<br />';
                    echo SwatString::minimizeEntities(
                        $this->getDependencyText($count)
                    );

                    echo '<ul>';
                    $first = false;
                }

                echo '<li>';

                if ($entry->content_type == 'text/xml') {
                    echo $entry->title;
                } else {
                    echo SwatString::minimizeEntities($entry->title);
                }

                foreach ($this->dependencies as $dep) {
                    $dep->displayDependencies($entry->id, $status_level);
                }

                echo '</li>';
            }
        }

        if ($count > 0) {
            echo '</ul>';
        }
    }

    /**
     * Displays all the dependency entries at a single status level for this
     * dependency.
     *
     * @param int $status_level the status level to display dependency entries for
     */
    protected function displayStatusLevel(int $status_level)
    {
        $count = $this->getStatusLevelCount($status_level);
        $first = true;
        foreach ($this->entries as $entry) {
            if ($entry->status_level == $status_level) {
                if ($first) {
                    $this->displayStatusLevelHeader($status_level, $count);
                    echo '<ul>';
                    $first = false;
                }

                echo '<li>';

                if ($entry->content_type == 'text/xml') {
                    echo $entry->title;
                } else {
                    echo SwatString::minimizeEntities($entry->title);
                }

                foreach ($this->dependencies as $dep) {
                    $dep->displayDependencies($entry->id, $status_level);
                }

                echo '</li>';
            }
        }
        if ($count > 0) {
            echo '</ul>';
        }
    }

    /**
     * Queries for dependency entries.
     *
     * Convenience method to query for an array of {@link AdminDependencyEntry}
     * objects. The returned entry array may be directly assigned to the
     * {@link AdminListDependency::$entries} property.
     *
     * For example:
     * <code>
     * $dep = new AdminListDependency();
     * $dep->entries = AdminListDependency::queryEntries($this->app->db,
     *     'orders', 'integer:id', 'integer:paymentmethod',
     *     'paymentmethod in (123, 456, 789)', AdminDependency::DELETE);
     * </code>
     *
     * @param MDB2_Driver_Common $db              the database connection
     * @param string             $table           the database table to query
     * @param string             $id_field        The name of the database field to query for
     *                                            the id. Can be given in the form type:name where type is a
     *                                            standard MDB2 datatype. If type is ommitted, then integer is
     *                                            assummed for this field.
     * @param string             $parent_field    The name of the database field to query to
     *                                            link the child dependencies to the parent, or null. The values
     *                                            in this field should correspond to entry ids in a parent
     *                                            AdminListDependency object. This field may be given in the form
     *                                            type:name where type is a standard MDB2 datatype. If type is
     *                                            ommitted, then integer is assummed for this field.
     * @param string             $title_field     The name of the database field to query for
     *                                            the title. Can be given in the form type:name where type is a
     *                                            standard MDB2 datatype. If type is ommitted, then text is
     *                                            assummed for this field.
     * @param string             $order_by_clause Optional comma deliminated list of
     *                                            database field names to use in the <i>order by</i> clause.
     *                                            Do not include "order by" in the string; only include the list
     *                                            of field names. Use the value <b>null</b> to ignore this
     *                                            paramater.
     * @param string             $where_clause    Optional <i>where</i> clause to limit the
     *                                            returned results. Do not include "where" in the string; only
     *                                            include the conditional statement.
     * @param int                $status_level    Optional status level to assign to the
     *                                            queried entries. If no status level is specified, the status
     *                                            level {@link AdminDependency::NODELETE} is used.
     *
     * @return array an array of {@link AdminDependencyEntry} objects
     */
    public static function queryEntries(
        $db,
        $table,
        $id_field,
        $parent_field,
        $title_field,
        $order_by_clause = null,
        $where_clause = null,
        $status_level = AdminDependency::NODELETE
    ) {
        $id_field = new SwatDBField($id_field, 'integer');
        $title_field = new SwatDBField($title_field, 'text');

        if ($parent_field === null) {
            $parent_value = 'null';
            $types = [$id_field->type, 'integer', $title_field->type, 'integer'];
        } else {
            $parent_field = new SwatDBField($parent_field, 'integer');
            $parent_value = $parent_field->name;
            $types = [
                $id_field->type,
                $parent_field->type,
                $title_field->type,
                'integer',
            ];
        }

        $sql = sprintf(
            'select %s as id, %s as parent, %s as title,
				%s as status_level from %s',
            $id_field->name,
            $parent_value,
            $title_field->name,
            $db->quote($status_level, 'integer'),
            $table
        );

        if ($where_clause !== null) {
            $sql .= ' where ' . $where_clause;
        }

        if ($order_by_clause !== null) {
            $sql .= ' order by ' . $order_by_clause;
        }

        $entries = SwatDB::query(
            $db,
            $sql,
            AdminDependencyEntryWrapper::class,
            $types
        );

        return $entries->getArray();
    }

    /**
     * Builds an array of dependency entries.
     *
     * Convenience method to create a flat array of {@link AdminDependencyEntry}
     * objects. The returned array of dependency entries may be directly
     * assigned to the {@link AdminListDependency::$entries} property of an
     * {@link AdminListDependency} object.
     *
     * @param array $items        an associative array of dependent items in the form
     *                            of id => title. This array is usually constructed
     *                            from the result of a database query.
     * @param array $parents      Optional associative array containing tree
     *                            information for the items array in the form of
     *                            id => parent. This array is usually constructed
     *                            from the result of a database query. If not
     *                            specified, all created entries will have a
     *                            parent of null.
     * @param int   $status_level Optional status level to assign to the
     *                            queried entries. If no status level is
     *                            specified, the status level
     *                            {@link AdminDependency::NODELETE} is used.
     *
     * @return array a flat array of {@link AdminDependencyEntry} objects that
     *               contains dependency tree information
     */
    public static function buildEntriesArray(
        $items,
        $parents = null,
        $status_level = AdminDependency::NODELETE
    ) {
        $entries = [];

        foreach ($items as $id => $title) {
            if ($parents === null || array_key_exists($id, $parents)) {
                $entry = new AdminDependencyEntry();
                $entry->id = $id;
                $entry->title = $title;
                $entry->parent = ($parents === null) ? null : $parents[$id];
                $entry->status_level = $status_level;

                $entries[] = $entry;
            }
        }

        return $entries;
    }

    /**
     * Gets the text for a dependency list for this dependency.
     *
     * Sub-classes may override this method to have more descriptive or
     * meaningful text.
     *
     * @param int $count the number of dependencies in the list
     *
     * @return string the text for a dependency list for this dependency
     */
    protected function getDependencyText($count)
    {
        $title = $this->getTitle($count);
        $message = Admin::ngettext(
            'Dependent %s:',
            'Dependent %s:',
            $count
        );

        return sprintf($message, $title);
    }
}
