<?php
require_once('SwatDB/SwatDBField.php');

/**
 * Class for building search clauses
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminSearchClause {

	const OP_EQUALS = '=';
	const OP_GT = '>';
	const OP_GTE = '>=';
	const OP_LT = '<';
	const OP_LTE = '<=';
	const OP_CONTAINS = 'like';
	const OP_STARTS_WITH = 'like';
	const OP_ENDS_WITH = 'like';

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
		return " {$logic_operator} {$field} {$this->operator} {$value} ";
	}

}

