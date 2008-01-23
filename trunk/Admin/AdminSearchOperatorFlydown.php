<?php

require_once 'Swat/SwatFlydown.php';
require_once 'Admin/Admin.php';
require_once 'Admin/AdminSearchClause.php';

/**
 * A flydown selection widget for search operators.
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 */
class AdminSearchOperatorFlydown extends SwatFlydown
{
	// {{{ public properties

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
	                          AdminSearchClause::OP_ENDS_WITH,
	                          AdminSearchClause::OP_EQUALS);

	// }}}
	// {{{ public function display()
	
	public function display()
	{
		if (!$this->visible)
			return;

		$this->options = array();
		$this->show_blank = false;

		foreach ($this->operators as $op)
			$this->addOption($op,
				AdminSearchOperatorFlydown::getOperatorTitle($op));

		parent::display();
	}

	// }}}
	// {{{ private static function getOperatorTitle()

	private static function getOperatorTitle($id)
	{
		switch ($id) {
			case AdminSearchClause::OP_EQUALS:      return Admin::_('is');
			case AdminSearchClause::OP_GT:          return '>';
			case AdminSearchClause::OP_GTE:         return '>=';
			case AdminSearchClause::OP_LT:          return '<';
			case AdminSearchClause::OP_LTE:         return '<=';
			case AdminSearchClause::OP_CONTAINS:    return Admin::_('contains');
			case AdminSearchClause::OP_STARTS_WITH: return Admin::_('starts with');
			case AdminSearchClause::OP_ENDS_WITH:   return Admin::_('ends with');

			default:
				throw new Exception('AdminSearchOperatorFlydown: unknown operator');
		}
	}

	// }}}
}

?>
