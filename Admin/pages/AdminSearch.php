<?php

require_once 'Admin/pages/AdminIndex.php';

/**
 * Generic admin search page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * search page, inherit directly from AdminPage instead.
 *
 * @package   Admin
 * @copyright 2004-2006 silverorange
 */
abstract class AdminSearch extends AdminIndex
{
	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		try {
			$form = $this->ui->getWidget('search_form');
			$form->process();

			if ($form->isProcessed())
				$this->saveState();

			if ($this->hasState()) {
				$this->loadState();
				$frame = $this->ui->getWidget('results_frame');
				$frame->visible = true;
			}
		} catch (SwatWidgetNotFoundException $e) {
		}
	}

	// }}}
	// {{{ protected function clearState()

	/**
	 * Clears a saved search state
	 */
	protected function clearState()
	{
		if ($this->hasState())
			unset($_SESSION[$this->source.'_search_state']);
	}

	// }}}
	// {{{ protected function saveState()

	protected function saveState()
	{
		try {
			$search_form = $this->ui->getWidget('search_form');
			$search_state = $search_form->getDescendantStates();
			$_SESSION[$this->source.'_search_state'] = $search_state;
		} catch (SwatWidgetNotFoundException $e) {
		}
	}

	// }}}
	// {{{ protected function loadState()

	/**
	 * Loads a saved search state for this page
	 *
	 * @return boolean true if a saved state exists for this page and false if
	 *                  it does not.
	 *
	 * @see AdminSearchPage::hasState()
	 */
	protected function loadState()
	{
		$return = false;

		try {
			$search_form = $this->ui->getWidget('search_form');
			$key = $this->source.'_search_state';

			if ($this->hasState()) {
				$search_form->setDescendantStates($_SESSION[$key]);
				$return = true;
			}
		} catch (SwatWidgetNotFoundException $e) {
		}

		return $return;
	}

	// }}}
	// {{{ protected function hasState()

	/**
	 * Checks if this search page has stored search information
	 *
	 * @return boolean true if this page has stored search information and
	 *                  false if it does not.
	 */
	protected function hasState()
	{
		$key = $this->source.'_search_state';
		return isset($_SESSION[$key]);
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		try {
			$form = $this->ui->getWidget('search_form', true);
			$form->action = $this->source;
		} catch (SwatWidgetNotFoundException $e) {
		}
	}

	// }}}
	// {{{ protected function buildViews()

	/**
	 * Builds views for this search page
	 *
	 * View models are initialized to an empty table store unless a saved
	 * search state is available.
	 */
	protected function buildViews()
	{
		try {
			$results_frame = $this->ui->getWidget('results_frame');

			// set non-visible results frame views to have empty models
			if (!$results_frame->visible) {
				$views = $results_frame->getDescendants('SwatTableView');
				foreach ($views as $view)
					$view->model = new SwatTableStore();
			}
		} catch (SwatWidgetNotFoundException $e) {
		}

		// build other views normally
		parent::buildViews();
	}

	// }}}
}

?>
