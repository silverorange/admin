<?php

require_once 'Admin/Admin/DBOrder.php';
require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Order page for AdminComponents
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminSectionsOrder extends AdminDBOrder {

	private $parent;

	public function init() {
		parent::init();

		$this->parent = SwatApplication::initVar('parent');
		$form = $this->ui->getWidget('order_form');
		$form->addHiddenField('parent', $this->parent);
	}

	public function initDisplay() {
		$frame = $this->ui->getWidget('order_frame');
		$frame->title = Admin::_('Order Sections');
		parent::initDisplay();
	}

	public function loadData() {
		$order_widget = $this->ui->getWidget('order');
		$order_widget->options = SwatDB::getOptionArray($this->app->db, 
			'adminsections', 'title', 'sectionid', 'displayorder, title');

		$sql = 'select sum(displayorder) from adminsections';
		$sum = SwatDB::queryOne($this->app->db, $sql, 'integer');
		$options_list = $this->ui->getWidget('options');
		$options_list->value = ($sum == 0) ? 'auto' : 'custom';
	}
	
	public function saveIndex($id, $index) {
		SwatDB::updateColumn($this->app->db, 'adminsections', 'integer:displayorder',
			$index, 'integer:sectionid', array($id));
	}
}

?>
