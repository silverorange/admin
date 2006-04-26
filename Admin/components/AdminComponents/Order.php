<?php

require_once 'Admin/pages/AdminDBOrder.php';
require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Order page for AdminComponents
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsOrder extends AdminDBOrder
{
	// {{{ private properties

	private $parent;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->parent = SiteApplication::initVar('parent');
		$form = $this->ui->getWidget('order_form');
		$form->addHiddenField('parent', $this->parent);
	}

	// }}}

	// process phase
	// {{{ protected function saveIndex()

	protected function saveIndex($id, $index)
	{
		SwatDB::updateColumn($this->app->db, 'admincomponents', 'integer:displayorder',
			$index, 'integer:id', array($id));
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()
	protected function buildInternal()
	{
		$frame = $this->ui->getWidget('order_frame');
		$frame->title = Admin::_('Order Components');
		parent::buildInternal();
	}

	// }}}
	// {{{ protected function loadData()

	protected function loadData()
	{ 
		$where_clause = sprintf('section = %s',
			$this->app->db->quote($this->parent, 'integer'));

		$order_widget = $this->ui->getWidget('order');
		$order_widget->addOptionsByArray(SwatDB::getOptionArray($this->app->db, 
			'admincomponents', 'title', 'id', 'displayorder, title', $where_clause));

		$sql = 'select sum(displayorder) from admincomponents where '.$where_clause;
		$sum = SwatDB::queryOne($this->app->db, $sql, 'integer');
		$options_list = $this->ui->getWidget('options');
		$options_list->value = ($sum == 0) ? 'auto' : 'custom';
	}

	// }}}
}

?>
