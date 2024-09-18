<?php

/**
 * A flydown selection widget for search operators.
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminSearchOperatorFlydown extends SwatFlydown
{


	/**
	 * Operators
	 *
	 * An array of operator constants to display as option. The constants are
	 * defined in {@link AdminSearchClause}.
	 *
	 * @var array
	 */
	public $operators = [
        AdminSearchClause::OP_CONTAINS,
        AdminSearchClause::OP_STARTS_WITH,
        AdminSearchClause::OP_ENDS_WITH,
        AdminSearchClause::OP_EQUALS
    ];



	public function display()
	{
		if (!$this->visible)
			return;

		$this->options = [];
		$this->show_blank = false;

		foreach ($this->operators as $op) {
			$this->addOption($op, self::getOperatorTitle($op));
		}

		parent::display();
	}



	private static function getOperatorTitle($id)
	{
		return match ($id) {
            AdminSearchClause::OP_EQUALS => Admin::_('is'),
            AdminSearchClause::OP_GT => '>',
            AdminSearchClause::OP_GTE => '>=',
            AdminSearchClause::OP_LT => '<',
            AdminSearchClause::OP_LTE => '<=',
            AdminSearchClause::OP_CONTAINS => Admin::_('contains'),
            AdminSearchClause::OP_STARTS_WITH => Admin::_('starts with'),
            AdminSearchClause::OP_ENDS_WITH => Admin::_('ends with'),
            default => throw new Exception('AdminSearchOperatorFlydown: unknown operator'),
        };
	}

}

?>
