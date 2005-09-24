<?php

require_once 'Admin/Admin.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/pages/AdminPage.php';

/**
 * Generic admin ordering page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * ordering page, inherit directly from AdminPage instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminOrder extends AdminPage
{
	public function process()
	{
		$this->ui->process();
		$form = $this->ui->getWidget('order_form');

		if ($form->isProcessed()) {
			$this->saveData();
			$this->app->relocate($this->app->history->getHistory());
		}
	}

	public function initDisplay()
	{
		$options_list = $this->ui->getWidget('options');
		$options_list->options = array('auto'=>Admin::_('Automatically'), 'custom'=>Admin::_('Custom'));
			
		$order_widget = $this->ui->getWidget('order');
		$order_widget->onclick = 'document.getElementById(\'options_custom\').checked = true;';
		
		$this->loadData();
	
		$button = $this->ui->getWidget('submit_button');
		$button->title = Admin::_('Update Order');
		
		$form = $this->ui->getWidget('order_form');
		$form->action = $this->source;
	}
	
	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/order.xml');

		$this->navbar->createEntry(Admin::_('Change Order'));
	}

	protected function saveData()
	{
		$count = 0;
		$order_widget = $this->ui->getWidget('order');
		$options_list = $this->ui->getWidget('options');

		foreach ($order_widget->values as $id) {
			if ($options_list->value == 'custom')
				$count++;

			$this->saveIndex($id, $count);
		}
	}

	/**
	 * Save index
	 *
	 * This method is called by {@link AdminOrder::saveData()} to save a single 
	 * ordering index. Sub-classes should implement this method and perform 
	 * whatever actions are necessary to store the ordering index. Widgets can
	 * be accessed through the $ui class variable.
	 *
	 * @param integer $id An integer identifier of the data to store.
	 * @param integer $index The ordering index to store.
	 */
	abstract protected function saveIndex($id, $index);

	/**
	 * Load the data
	 *
	 * This method is called to load data to be edited into the widgets.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to obtain the data. Widgets can be accessed through the
	 * $ui class variable.
	 */
	abstract protected function loadData();
}

?>
