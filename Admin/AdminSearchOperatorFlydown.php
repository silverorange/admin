<?php

require_once 'Swat/SwatFlydown.php';
require_once 'Admin/AdminSearchClause.php';

/**
 * A flydown selection widget for search operators.
 *
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminSearchOperatorFlydown extends SwatFlydown {

	/**
	 * Operators
	 *
	 * An array of operator constants to display as option. The constants are 
	 * defined in {@link AdminSearchClause}.
	 *
	 * @var array
	 */
	public $operators = array(AdminSearchClause::OP_CONTAINS,
	                          AdminSearchClause::OP_STARTS_WITH,
	                          AdminSearchClause::OP_ENDS_WITH);
	
	public function display() {
		$this->options = array();
		$this->show_blank = false;

		foreach ($this->operators as $op)
			$this->options[$op] = AdminSearchOperatorFlydown::getOperatorTitle($op);

		parent::display();
	}

	private static function getOperatorTitle($id) {
		switch ($id) {
			case AdminSearchClause::OP_EQUALS:      return '=';
			case AdminSearchClause::OP_GT:          return '>';
			case AdminSearchClause::OP_GTE:         return '>=';
			case AdminSearchClause::OP_LT:          return '<';
			case AdminSearchClause::OP_LTE:         return '<=';
			case AdminSearchClause::OP_CONTAINS:    return _S("contains");
			case AdminSearchClause::OP_STARTS_WITH: return _S("starts with");
			case AdminSearchClause::OP_ENDS_WITH:   return _S("ends with");

			default:
				throw new Exception('AdminSearchOperatorFlydown: unknown operator');
		}
	}

}

?>
