<?php

require_once 'Admin/AdminPage.php';

/**
 * Generic admin index page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * index page, inherit directly from AdminPage instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminIndex extends AdminPage
{
	public function initDisplay()
	{
		$view = $this->ui->getWidget('index_view');
		$view->model = $this->getTableStore();

		$form = $this->ui->getWidget('index_form');
		$form->action = $this->source;

		if ($view->model->getRowCount() == 0) {
			$actions = $this->ui->getWidget('index_actions');
			$actions->visible = false;
		}

		$this->initMessages();
	}

	public function process()
	{
		$this->ui->process();

		$form = $this->ui->getWidget('index_form');
		$view = $this->ui->getWidget('index_view');
		$actions = $this->ui->getWidget('index_actions', true);

		if (!$form->hasBeenProcessed())
			return;

		if (count($view->checked_items) == 0)
			return;

		if ($actions !== null) {
			if ($actions->selected === null)
				return;

			$this->processActions();
		}
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

	protected function getOrderByClause($default_orderby, $column_prefix = null, $column_map = array())
	{
		$view = $this->ui->getWidget('index_view');
		$orderby = $default_orderby;

		if ($view->orderby_column !== null) {

			if ($view->orderby_column->id === null) 
				throw new SwatException('Orderable column missing id');
			elseif (isset($column_map[$view->orderby_column->id]))
				$orderby = $column_map[$view->orderby_column->id];
			elseif ($column_prefix !== null)
				$orderby = $column_prefix.'.'.$this->app->db->escape($view->orderby_column->id);
			else
				$orderby = $this->app->db->escape($view->orderby_column->id);

			$orderby .= ' '.$view->orderby_column->getDirectionAsString();
		}

		return $orderby;
	}

	/**
	 * Process the actions.
	 *
	 * This method is called to perform whatever processing is required in 
	 * response to actions. Sub-classes should implement this method.
	 * Widgets can be accessed through the $ui class variable.
	 */
	protected function processActions()
	{
	}
}

?>
