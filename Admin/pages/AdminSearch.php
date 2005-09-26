<?php

require_once 'Admin/pages/AdminIndex.php';

/**
 * Generic admin search page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * search page, inherit directly from AdminPage instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminSearch extends AdminIndex
{
	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		$form = $this->ui->getWidget('search_form', true);

		if ($form !== null) {
			$form->process();

			if ($form->isProcessed())
				$this->saveState();

			if ($this->loadState()) {
				$index = $this->ui->getWidget('results_frame');
				$index->visible = true;
			}
		}
	}

	// }}}
	// {{{ protected function saveState()

	protected function saveState()
	{
		$search_form = $this->ui->getWidget('search_form');
		$search_state = $search_form->getDescendantStates();
		$_SESSION[$this->source.'_search_state'] = $search_state;
	}

	// }}}
	// {{{ protected function loadState()

	protected function loadState()
	{
		$ret = false;
		$search_form = $this->ui->getWidget('search_form');
		$key = $this->source.'_search_state';

		if (isset($_SESSION[$key])) {
			$search_form->setDescendantStates($_SESSION[$key]);
			$ret = true;
		}

		return $ret;
	}

	// }}}

	// build phase
	// {{{ protected function initDisplay()

	protected function initDisplay()
	{
		parent::initDisplay();

		$form = $this->ui->getWidget('search_form', true);

		if ($form !== null)
			$form->action = $this->source;
	}

	// }}}
}

?>
