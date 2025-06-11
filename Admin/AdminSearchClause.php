<?php

/**
 * Object for building SQL search clauses.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminSearchClause
{
    public const OP_EQUALS = 1;
    public const OP_GT = 2;
    public const OP_GTE = 3;
    public const OP_LT = 4;
    public const OP_LTE = 5;
    public const OP_CONTAINS = 6;
    public const OP_STARTS_WITH = 7;
    public const OP_ENDS_WITH = 8;

    /**
     * Database field of this search clause.
     *
     * @var SwatDBField
     */
    private $field;

    /**
     * Value of this search clause.
     *
     * This value is always passed through PHP's trim() function.
     *
     * @var mixed
     */
    public $value;

    /**
     * Table prefix for the field.
     *
     * @var string
     */
    public $table;

    /**
     * Whether or not the search match should be case-sensitive.
     *
     * @var bool
     */
    public $case_sensitive = false;

    /**
     * Search operator.
     *
     * This value should be one of the AdminSearchClause::OP_* constants.
     *
     * @var int
     */
    public $operator = self::OP_EQUALS;

    /**
     * Creates a new search clause object.
     *
     * @param string $field a string representation of the database field to
     *                      search in the form [<type>:]<name> where <name> is
     *                      the name of the database field and <type> is any
     *                      standard MDB2 datatype
     * @param mixed  $value the value to search for
     */
    public function __construct($field, $value = null)
    {
        $this->field = new SwatDBField($field);
        $this->value = $value;
    }

    /**
     * Gets this search clause as a string that can be included in a SQL
     * 'where' clause.
     *
     * @param MDB2_Driver_Common the database connection to use. This is used
     *                            for correctly quoting the value of this
     *                            search clause.
     * @param string $logic_operator optional. The logical operator for this
     *                               search clause. If no logical operator is
     *                               needed, use a blank string. Defaults to
     *                               'and'.
     *
     * @return string this search clause as a string that can be included in a
     *                SQL 'where' clause
     */
    public function getClause(MDB2_Driver_Common $db, $logic_operator = 'and')
    {
        if ($this->value === null) {
            return '';
        }

        $field = ($this->table === null) ? '' : $this->table . '.';
        $field .= $this->field->name;
        $value = trim($this->value);

        if ($this->field->type == 'text') {
            if (trim($this->value) == '') {
                return null;
            }

            if (!$this->case_sensitive) {
                $field = 'lower(' . $field . ')';
                $value = mb_strtolower($value);
            }

            if ($this->operator == self::OP_CONTAINS) {
                $value = "%{$value}%";
            } elseif ($this->operator == self::OP_STARTS_WITH) {
                $value = "{$value}%";
            } elseif ($this->operator == self::OP_ENDS_WITH) {
                $value = "%{$value}";
            }
        } elseif ($this->field->type == 'integer') {
            if (trim((string) $this->value) == '') {
                return null;
            }
        } elseif ($this->field->type == 'date') {
            if (is_object($value) && $value instanceof SwatDate) {
                $value = $value->getDate();
            }
        }

        $value = $db->quote($value, $this->field->type);
        $operator = self::getOperatorString($this->operator);

        return " {$logic_operator} {$field} {$operator} {$value} ";
    }

    /**
     * Gets a search clause operator constant as an SQL string.
     *
     * @param int $operator the search clause operator to get as an SQL
     *                      string
     *
     * @return string the search clause operator as an SQL string. For example,
     *                {AdminSearchClause::OP_GTE} returns '&gt;='.
     */
    private static function getOperatorString($operator)
    {
        $operator = intval($operator);

        switch ($operator) {
            case self::OP_EQUALS:
                return '=';

            case self::OP_GT:
                return '>';

            case self::OP_GTE:
                return '>=';

            case self::OP_LT:
                return '<';

            case self::OP_LTE:
                return '<=';

            case self::OP_CONTAINS:
                return 'like';

            case self::OP_STARTS_WITH:
                return 'like';

            case self::OP_ENDS_WITH:
                return 'like';

            default:
                throw new AdminException('Unknown operator in clause: ' . $operator);
        }
    }
}
