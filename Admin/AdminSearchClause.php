<?php
require_once('SwatDB/SwatDBField.php');

/**
 * Class for building search clauses
 *
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminSearchClause {

	const OP_EQUALS      = 1;
	const OP_GT          = 2;
	const OP_GTE         = 3;
	const OP_LT          = 4;
	const OP_LTE         = 5;
	const OP_CONTAINS    = 6;
	const OP_STARTS_WITH = 7;
	const OP_ENDS_WITH   = 8;

	private $field;
	public $value;
	public $case_sensitive = false;
	public $operator = AdminSearchClause::OP_EQUALS;

	/**
	 * The database object
	 *
	 * @param string $field A string representation of a database field in the
	 *        form [<type>:]<name> where <name> is the name of the database 
	 *        field and <type> is any standard MDB2 datatype.
	 *
	 * @param mixed $value The value of the search clause.
	 */
	function __construct($field, $value = null) {
		$this->field = new SwatDBField($field);
		$this->value = $value;
	}


	/**
	 * Get a formatted search clause
	 *
	 * @param MDB2_Connection Database connection object (readonly)
	 * 
	 * @return string SQL search clause
	 */
	public function getClause($db, $logic_operator = 'and') {
		if ($this->value === null)
			return '';
		
		$field = $this->field->name;
		$value = $this->value;
	
		if ($this->field->type == 'text') {
			if (!$this->case_sensitive) {
				$field = 'lower('.$field.')';
				$value = strtolower($value);
			}

			if ($this->operator == AdminSearchClause::OP_CONTAINS)
				$value = "%{$value}%";
			elseif ($this->operator == AdminSearchClause::OP_STARTS_WITH)
				$value = "{$value}%";
			elseif ($this->operator == AdminSearchClause::OP_ENDS_WITH)
				$value = "%{$value}";
		}

		$value = $db->quote($value);
		$operator = AdminSearchClause::getOperatorString($this->operator);
		$clause = " {$logic_operator} {$field} {$operator} {$value} ";

		return $clause;
	}

	private static function getOperatorString($id) {
		switch ($id) {
			case AdminSearchClause::OP_EQUALS:      return '=';
			case AdminSearchClause::OP_GT:          return '>';
			case AdminSearchClause::OP_GTE:         return '>=';
			case AdminSearchClause::OP_LT:          return '<';
			case AdminSearchClause::OP_LTE:         return '<=';
			case AdminSearchClause::OP_CONTAINS:    return 'like';
			case AdminSearchClause::OP_STARTS_WITH: return 'like';
			case AdminSearchClause::OP_ENDS_WITH:   return 'like';

			default:
				throw new Exception('AdminSearchClause: unknown operator');
		}
	}
}

