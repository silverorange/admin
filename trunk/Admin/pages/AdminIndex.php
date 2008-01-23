<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Swat/SwatView.php';
require_once 'Swat/SwatActions.php';

/**
 * Generic admin index page
 *
 * This class is intended to be a convenience base class. For a fully custom
 * index page, inherit directly from AdminPage instead.
 *
 * @package   Admin
 * @copyright 2005-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminIndex extends AdminPage
{
	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		$forms = $this->ui->getRoot()->getDescendants('SwatForm');

		foreach ($forms as $form) {
			if ($form->isProcessed()) {
				if (!$form->isAuthenticated()) {
					$message = new SwatMessage(Admin::_(
						'There is a problem with the information submitted.'),
						SwatMessage::WARNING);

					$message->secondary_content =
						Admin::_('In order to ensure your security, we were '.
						'unable to process your request. Please try again.');

					$this->app->messages->add($message);
				} else {
					$view = $form->getFirstDescendant('SwatView');
					$actions = $form->getFirstDescendant('SwatActions');

					if (($view !== null) &&
						(count($view->getSelection()) > 0) &&
						($actions !== null) && ($actions->selected !== null))
						$this->processActions($view, $actions);
				}

				// only one form can be processed in a single request
				break;
			}
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
	 * @param SwatView $view the view to get selected items from.
	 * @param SwatActions $actions the actions list widget.
	 */
	protected function processActions(SwatView $view, SwatActions $actions)
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
		$views = $root->getDescendants('SwatView');
		foreach ($views as $view)
			if ($view->model === null)
				$view->model = $this->getTableModel($view);
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
			$view = $form->getFirstDescendant('SwatView');
			$actions = $form->getFirstDescendant('SwatActions');

			if ($view !== null && $view->model !== null && $actions !== null) {
				$input_row =
					$view->getFirstDescendant('SwatTableViewInputRow');

				if (count($view->model) == 0) {
					$actions->visible = false;
				} elseif ($input_row === null) {
					$actions->setViewSelector($view);
				}
			}
		}
	}

	// }}}
	// {{{ abstract protected function getTableModel()

	/**
	 * Retrieve data to display.
	 *
	 * This method is called to load data to be displayed in the table view.
	 * Sub-classes should implement this method and perform whatever actions
	 * are necessary to obtain the data.
	 *
	 * @return SwatTableModel A new SwatTableModel containing the data.
	 *
	 */
	abstract protected function getTableModel(SwatView $view);

	// }}}
	// {{{ protected function getOrderByClause()

	protected function getOrderByClause($view, $default_orderby,
		$column_prefix = null, $column_map = array())
	{
		$orderby = $default_orderby;

		if ($view instanceof SwatTableView &&  $view->orderby_column !== null) {
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
