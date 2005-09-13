<?php

require_once 'Admin/Admin/DBOrder.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Order page for AdminSubComponents
 *
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminSubComponentsOrder extends AdminDBOrder
{
	private $parent;

	public function initDisplay()
	{
		$frame = $this->ui->getWidget('order_frame');
		$frame->title = Admin::_('Order Sub-Components');
		parent::initDisplay();
	
		// rebuild the navbar
		$parent_title = SwatDB::queryOneFromTable($this->app->db, 'admincomponents', 'text:title',
			'id', $this->parent);

		// pop two entries because the AdminDBOrder base class adds an entry
		$this->navbar->popEntries(2);
		$this->navbar->createEntry('Admin Components', 'AdminComponents');
		$this->navbar->createEntry($parent_title, 'AdminComponents/Details?id='.$this->parent);
		$this->navbar->createEntry('Order Sub-Components');
	}

	public function loadData()
	{
		$where_clause = sprintf('component = %s',
			$this->app->db->quote($this->parent, 'integer'));

		$order_list = $this->ui->getWidget('order');
		$order_list->options = SwatDB::getOptionArray($this->app->db, 'adminsubcomponents', 
			'title', 'id', 'displayorder, title', $where_clause);

		$sql = 'select sum(displayorder) from adminsubcomponents where '.$where_clause;
		$sum = SwatDB::queryOne($this->app->db, $sql, 'integer');
		$radio_list = $this->ui->getWidget('options');
		$radio_list->value = ($sum == 0) ? 'auto' : 'custom';
	}
	
	public function saveIndex($id, $index)
	{
		SwatDB::updateColumn($this->app->db, 'adminsubcomponents', 'integer:displayorder',
			$index, 'integer:id', array($id));
	}

	protected function initInternal()
	{
		parent::initInternal();

		$this->parent = SwatApplication::initVar('parent');
		$form = $this->ui->getWidget('order_form');
		$form->addHiddenField('parent', $this->parent);
	}
}

?>
