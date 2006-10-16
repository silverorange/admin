<?php

require_once 'SwatDB/SwatDBField.php';
require_once 'Admin/exceptions/AdminException.php';

/**
 * Class for building search clauses
 *
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminSearchClause
{
	// {{{ constants

	const OP_EQUALS      = 1;
	const OP_GT          = 2;
	const OP_GTE         = 3;
	const OP_LT          = 4;
	const OP_LTE         = 5;
	const OP_CONTAINS    = 6;
	const OP_STARTS_WITH = 7;
	const OP_ENDS_WITH   = 8;

	// }}}
	// {{{ private properties

	private $field;

	// }}}
	// {{{ public properties
	
	/**
	 * Value of the search clause
	 *
	 * this value is always passed through trim
	 *
	 * @var mixed
	 */
	public $value;
	
	/**
	 * Table prefix for the field.
	 *
	 * @var string
	 */
	public $table = null;
	
	/**
	 * Case sensitive
	 *
	 * Whether or not the search match should be case-sensitive
	 *
	 * @var boolean
	 */
	public $case_sensitive = false;
	
	/**
	 * Search operator
	 *
	 * Set using one of the {@link AdminSearchClause} class constants
	 *
	 * @var string
	 */
	public $operator;

	// }}}
	// {{{ publuc function __construct()

	/**
	 * The database object
	 *
	 * @param string $field A string representation of a database field in the
	 *        form [<type>:]<name> where <name> is the name of the database 
	 *        field and <type> is any standard MDB2 datatype.
	 *
	 * @param mixed $value The value of the search clause.
	 */
	function __construct($field, $value = null)
	{
		$this->field = new SwatDBField($field);
		$this->value = $value;
		$this->operator = self::OP_EQUALS;
	}

	// }}}
	// {{{ public function getClause()

	/**
	 * Get a formatted search clause
	 *
	 * @param MDB2_Connection Database connection object (readonly)
	 * 
	 * @return string SQL search clause
	 */
	public function getClause($db, $logic_operator = 'and')
	{
		if ($this->value === null || strlen(trim($this->value)) == 0)
			return '';
		
		$field = ($this->table === null) ? '' : $this->table.'.';
		$field .= $this->field->name;
		$value = trim($this->value);
	
		if ($this->field->type == 'text') {
			if (!$this->case_sensitive) {
				$field = 'lower('.$field.')';
				$value = strtolower($value);
			}

			if ($this->operator == self::OP_CONTAINS)
				$value = "%{$value}%";
			elseif ($this->operator == self::OP_STARTS_WITH)
				$value = "{$value}%";
			elseif ($this->operator == self::OP_ENDS_WITH)
				$value = "%{$value}";

		} elseif ($this->field->type == 'date') {
			if (is_object($value) && $value instanceof SwatDate)
				$value = $value->getDate();
		}

		$value = $db->quote($value, $this->field->type);
		$operator = self::getOperatorString($this->operator);
		$clause = " {$logic_operator} {$field} {$operator} {$value} ";

		return $clause;
	}

	// }}}
	// {{{ private static function getOperatorString()

	private static function getOperatorString($id)
	{
		$id = intval($id);

		switch ($id) {
			case self::OP_EQUALS:      return '=';
			case self::OP_GT:          return '>';
			case self::OP_GTE:         return '>=';
			case self::OP_LT:          return '<';
			case self::OP_LTE:         return '<=';
			case self::OP_CONTAINS:    return 'like';
			case self::OP_STARTS_WITH: return 'like';
			case self::OP_ENDS_WITH:   return 'like';

			default:
				throw new AdminException('Unknown operator in clause: '.$id);
		}
	}

	// }}}
}

?>
