<?php

require_once("Admin/Admin/Order.php");
require_once('Admin/AdminUI.php');
require_once('Admin/AdminDB.php');
require_once("MDB2.php");

/**
 * Order page for AdminComponents
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsOrder extends AdminOrder {

	public function loadData() {
		$order_list = $this->ui->getWidget('order');
		$order_list->options = AdminDB::getOptionArray($this->app->db, 
			'admincomponents', 'title', 'componentid', 'displayorder, title');

		$sum = $this->app->db->queryOne('select sum(displayorder) from admincomponents', 'integer');
		$radio_list = $this->ui->getWidget('options');
		$radio_list->value = ($sum == 0) ? 'auto' : 'custom';
	}
	
	public function saveIndex($id, $index) {
		AdminDB::updateField($this->app->db, 'admincomponents', 'integer:displayorder',
			$index, 'integer:componentid', array($id));
	}
}
?>
