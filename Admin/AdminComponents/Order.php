<?php

require_once('Admin/Admin/Order.php');
require_once('Admin/AdminUI.php');
require_once('SwatDB/SwatDB.php');

/**
 * Order page for AdminComponents
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsOrder extends AdminOrder {

	private $parent;

	public function init() {
		parent::init();

		$this->parent = SwatApplication::initVar('parent');
		$form = $this->ui->getWidget('order_form');
		$form->addHiddenField('parent', $this->parent);
	}

	public function display() {
		$frame = $this->ui->getWidget('order_frame');
		$frame->title = _S("Order Components");
		parent::display();
	}

	public function loadData() {
		$where_clause = sprintf('section = %s',
			$this->app->db->quote($this->parent, 'integer'));

		$order_widget = $this->ui->getWidget('order');
		$order_widget->options = SwatDB::getOptionArray($this->app->db, 
			'admincomponents', 'title', 'componentid', 'displayorder, title', $where_clause);

		$sql = 'select sum(displayorder) from admincomponents where '.$where_clause;
		$sum = $this->app->db->queryOne($sql, 'integer');
		$options_list = $this->ui->getWidget('options');
		$options_list->value = ($sum == 0) ? 'auto' : 'custom';
	}
	
	public function saveIndex($id, $index) {
		SwatDB::updateColumn($this->app->db, 'admincomponents', 'integer:displayorder',
			$index, 'integer:componentid', array($id));
	}
}
?>
