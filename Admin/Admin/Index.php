<?php

require_once("Admin/AdminPage.php");

/**
 * Generic admin index page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * index page, inherit directly from AdminPage instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminIndex extends AdminPage {

	protected $ui;

	public function display() {
		$view = $this->ui->getWidget('view');
		$view->model = $this->getTableStore();

		$form = $this->ui->getWidget('indexform');
		$form->action = $this->source;

		$mb = $this->ui->getWidget('messagebox', true);

		if ($mb != null)
			$mb->content = $this->app->getMessage();

		$root = $this->ui->getRoot();
		$root->display();
	}

	/**
	 * Retrieve data to display.
	 *
	 * This method is called to load data to be displayed in the table view.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to obtain the data.
	 *
	 * @return SwatTableStore A new SwatTableStore containing the data.
	 */
	abstract protected function getTableStore();

	protected function getOrderByClause($default_orderby, $column_prefix = null, $column_map = array()) {
		$view = $this->ui->getWidget('view');
		$orderby = $default_orderby;

		if ($view->orderby_column !== null) {

			if (isset($column_map[$view->orderby_column->name]))
				$orderby = $column_map[$view->orderby_column->name];
			elseif ($column_prefix !== null)
				$orderby = $column_prefix.'.'.$this->app->db->escape($view->orderby_column->name);
			else
				$orderby = $this->app->db->escape($view->orderby_column->name);

			$orderby .= ' '.$view->orderby_column->getDirectionString();
		}

		return $orderby;
	}

	public function process() {
		$form = $this->ui->getWidget('indexform');
		$view = $this->ui->getWidget('view');
		$actions = $this->ui->getWidget('actions', true);

		if (!$form->process())
			return;

		if (count($view->checked_items) == 0)
			return;

		if ($actions != null) {
			if ($actions->selected == null)
				return;

			$this->processActions();
		}
	}

	/**
	 * Process the actions.
	 *
	 * This method is called to perform whatever processing is required in 
	 * response to actions. Sub-classes should implement this method.
	 * Widgets can be accessed through the $ui class variable.
	 */
	protected function processActions() {

	}
}

?>
