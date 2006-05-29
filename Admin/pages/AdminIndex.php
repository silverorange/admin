<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Swat/SwatTableView.php';
require_once 'Swat/SwatActions.php';

/**
 * Generic admin index page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * index page, inherit directly from AdminPage instead.
 *
 * @package   Admin
 * @copyright 2004 silverorange
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
	 * Processes index actions
	 *
	 * This method is called to perform whatever processing is required in 
	 * response to actions. Sub-classes should implement this method.
	 * Widgets can be accessed through the $ui class variable.
	 *
	 * @param SwatTableView $view the table view to get selected items from.
	 * @param SwatActions $actions the actions list widget.
	 */
	protected function processActions(SwatTableView $view, SwatActions $actions)
	{
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$this->buildViews();
		$this->buildForms();
		$this->buildMessages();
	}

	// }}}
	// {{{ protected function buildViews()

	/**
	 * Builds tables views for this index page
	 */
	protected function buildViews()
	{
		$root = $this->ui->getRoot();
		$views = $root->getDescendants('SwatTableView');
		foreach ($views as $view)
			if ($view->model === null)
				$view->model = $this->getTableStore($view);
	}

	// }}}
	// {{{ protected function buildForms()

	/**
	 * Builds forms for this index page
	 */
	protected function buildForms()
	{
		$root = $this->ui->getRoot();
		$forms = $root->getDescendants('SwatForm');
		foreach ($forms as $form) {
			$form->action = $this->getRelativeURL();
			$view = $form->getFirstDescendant('SwatTableView');

			if ($view !== null && $view->model !== null && $view->model->getRowCount() == 0) {
				$actions = $form->getFirstDescendant('SwatActions');
				if ($actions !== null)
					$actions->visible = false;
			}
		}
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
