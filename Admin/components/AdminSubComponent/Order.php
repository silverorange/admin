<?php

/**
 * Order page for AdminSubComponents
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminSubComponentOrder extends AdminDBOrder
{


	private $parent;


	// init phase


	protected function initInternal()
	{
		parent::initInternal();

		$this->parent = SiteApplication::initVar('parent');
		$form = $this->ui->getWidget('order_form');
		$form->addHiddenField('parent', $this->parent);
	}


	// process phase


	protected function saveIndex($id, $index)
	{
		SwatDB::updateColumn($this->app->db, 'AdminSubComponent',
			'integer:displayorder', $index, 'integer:id', array($id));
	}


	// build phase


	protected function buildInternal()
	{
		parent::buildInternal();

		$frame = $this->ui->getWidget('order_frame');
		$frame->title = Admin::_('Order Sub-Components');

		// rebuild the navbar
		$parent_title = SwatDB::queryOneFromTable($this->app->db,
			'AdminComponent', 'text:title', 'id', $this->parent);

		// pop two entries because the AdminDBOrder base class adds an entry
		$this->navbar->popEntries(2);
		$this->navbar->createEntry('Admin Components', 'AdminComponent');
		$this->navbar->createEntry($parent_title,
			'AdminComponent/Details?id='.$this->parent);

		$this->navbar->createEntry('Order Sub-Components');
	}



	protected function loadData()
	{
		$where_clause = sprintf('component = %s',
			$this->app->db->quote($this->parent, 'integer'));

		$order_list = $this->ui->getWidget('order');
		$order_list_options = SwatDB::getOptionArray($this->app->db,
			'AdminSubComponent', 'title', 'id', 'displayorder, title',
			$where_clause);

		$order_list->addOptionsByArray($order_list_options);

		$sql = 'select sum(displayorder) from AdminSubComponent
			where '.$where_clause;

		$sum = SwatDB::queryOne($this->app->db, $sql, 'integer');
		$radio_list = $this->ui->getWidget('options');
		$radio_list->value = ($sum == 0) ? 'auto' : 'custom';
	}

}

?>
