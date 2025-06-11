<?php

/**
 * A dependency that displays a one line summary of dependent items.
 *
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminSummaryDependency extends AdminDependency
{
    /**
     * Array of {@link AdminDependencySummary} objects to be displayed.
     *
     * While this is a flat array, the objects in the array contain tree
     * structure information in their properties.
     *
     * Such an array may be automatically constructed from database data by
     * calling the static convenience method
     * {@link AdminSummaryDependency::querySummaries()}.
     *
     * @var array
     *
     * @see AdminDependencySummary, AdminDependencySummaryWrapper
     */
    public $summaries = [];

    /**
     * Gets the number of items in this dependency at a given status level.
     *
     * The number of items is calculated as the total summary count at the
     * given status level.
     *
     * @param int $status_level the status level to count items in
     *
     * @return int the number of items at the given status level in this
     *             dependency
     */
    public function getStatusLevelCount($status_level)
    {
        $count = 0;
        foreach ($this->summaries as &$summary) {
            if ($summary->status_level === $status_level) {
                $count += $summary->count;
            }
        }

        return $count;
    }

    /**
     * Counts the number of items in this dependency.
     *
     * The count is calculated as the total of all the summary counts.
     *
     * @return int the number of items in this dependency
     */
    public function getItemCount()
    {
        $total = 0;
        foreach ($this->summaries as &$summary) {
            $total += $summary->count;
        }

        return $total;
    }

    /**
     * Processes the status level of summaries in this dependency.
     *
     * @param $parent mixed the parent identifier of summaries to process
     *
     * @see AdminDependency::processItemStatuses()
     */
    public function processItemStatuses($parent = null)
    {
        $return = self::DELETE;

        foreach ($this->summaries as &$summary) {
            if ($parent === null || $summary->parent === $parent) {
                $return = max($return, $summary->status_level);
            }
        }

        return $return;
    }

    /**
     * Displays dependency summaries for the given parent at a given status
     * level.
     *
     * @param int $parent       the id of the parent to display summaries for
     * @param int $status_level the status level to display the summaries
     *                          for
     */
    public function displayDependencies($parent, $status_level)
    {
        $count = 0;
        foreach ($this->summaries as $summary) {
            if ($summary->parent == $parent
                && $summary->status_level === $status_level) {
                $count += $summary->count;
            }
        }

        if ($count > 0) {
            $span_tag = new SwatHtmlTag('span');
            $span_tag->class = 'admin-light';
            $span_tag->setContent($this->getDependencyText($count));

            $span_tag->open();
            echo ' (';
            $span_tag->displayContent();
            echo ')';
            $span_tag->close();
        }
    }

    /**
     * @param mixed      $db
     * @param mixed      $table
     * @param mixed      $id_field
     * @param mixed      $parent_field
     * @param mixed|null $where_clause
     * @param mixed      $status_level
     */
    public static function &querySummaries(
        $db,
        $table,
        $id_field,
        $parent_field,
        $where_clause = null,
        $status_level = 0
    ) {
        $id_field = new SwatDBField($id_field, 'integer');

        if ($parent_field === null) {
            $parent_value = 'null';
            $types = [$id_field->type, 'integer', 'integer'];
        } else {
            $parent_field = new SwatDBField($parent_field, 'integer');
            $parent_value = $parent_field->name;
            $types = [$id_field->type, $parent_field->type, 'integer'];
        }

        $sql = sprintf(
            'select count(%s) as count, %s as parent,
				%s::integer as status_level from %s',
            $id_field->name,
            $parent_value,
            $status_level,
            $table
        );

        if ($where_clause !== null) {
            $sql .= ' where ' . $where_clause;
        }

        if ($parent_field !== null) {
            $sql .= ' group by ' . $parent_field->name;
        }

        $entries = SwatDB::query(
            $db,
            $sql,
            'AdminDependencySummaryWrapper',
            $types
        );

        $entry_array = $entries->getArray();

        return $entry_array;
    }

    /**
     * Gets the text for a dependency list summary for this dependency.
     *
     * Sub-classes are encouraged to override this method to provide more
     * descriptive or meaningful text.
     *
     * @param int $count the number of items
     *
     * @return string the text for a summary item in this dependency
     */
    protected function getDependencyText($count)
    {
        $title = $this->getTitle($count);
        $message = Admin::ngettext(
            '%s dependent %s',
            '%s dependent %s',
            $count
        );

        return sprintf($message, SwatString::numberFormat($count), $title);
    }
}
