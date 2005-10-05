<?php

require_once 'Admin/pages/AdminPage.php';

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
	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();
		$root = $this->ui->getRoot();
		$forms = $root->getDescendants('SwatForm');

		foreach ($forms as $form) {
			$view = $form->getFirstDescendant('SwatTableView');
			$actions = $form->getFirstDescendant('SwatActions');

			if ($form->isProcessed() &&
				($view !== null) && (count($view->checked_items) != 0) &&
				($actions !== null) && ($actions->selected !== null))
					$this->processActions($view, $actions);
		}
	}

	// }}}
	// {{{ protected function processActions()

	/**
	 * Process the actions.
	 *
	 * This method is called to perform whatever processing is required in 
	 * response to actions. Sub-classes should implement this method.
	 * Widgets can be accessed through the $ui class variable.
	 */
	protected function processActions($form_id)
	{
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();
		$root = $this->ui->getRoot();
		$views = $root->getDescendants('SwatTableView');
		$forms = $root->getDescendants('SwatForm');

		foreach ($views as $view)
			$view->model = $this->getTableStore($view);

		foreach ($forms as $form) {
			$form->action = $this->source;
			$view = $form->getFirstDescendant('SwatTableView');

			if ($view !== null && $view->model->getRowCount() == 0) {
				$actions = $form->getFirstDescendant('SwatActions');

				if ($actions !== null)
					$actions->visible = false;
			}
		}

		$this->initMessages();
	}

	// }}}
	// {{{ protected function getTableStore()

	/**
	 * Retrieve data to display.
	 *
	 * This method is called to load data to be displayed in the table view.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to obtain the data.
	 *
	 * @return SwatTableStore A new SwatTableStore containing the data.
	 */
	abstract protected function getTableStore($view);

	// }}}
	// {{{ protected function getOrderByClause()

	protected function getOrderByClause($view, $default_orderby,
		$column_prefix = null, $column_map = array())
	{
		$orderby = $default_orderby;

		if ($view->orderby_column !== null) {
			if (isset($column_map[$view->orderby_column->id])) {
				$orderby = $column_map[$view->orderby_column->id];
			} elseif ($column_prefix !== null) {
				$orderby = $column_prefix.'.'.
					$this->app->db->escape($view->orderby_column->id);
			} else {
				$orderby = $this->app->db->escape($view->orderby_column->id);
			}

			$orderby .= ' '.$view->orderby_column->getDirectionAsString();
		}

		return $orderby;
	}

	// }}}
}

?>
