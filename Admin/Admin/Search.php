<?php

require_once("Admin/Admin/Index.php");

/**
 * Generic admin search page
 *
 * This class is intended to be a convenience base class. For a fully custom 
 * search page, inherit directly from AdminPage instead.
 *
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminSearch extends AdminIndex {

	public function process() {
		$form = $this->ui->getWidget('search_form', true);

		if ($form !== null) {
			if ($form->process()) {
				$this->saveState();
			}
		}

		parent::process();
	}

	public function displayInit() {
		$form = $this->ui->getWidget('search_form', true);

		if ($form !== null) {
			if ($this->loadState()) {
				$index = $this->ui->getWidget('results_frame');
				$index->visible = true;
			}
			$form->action = $this->source;
		}

		parent::displayInit();
	}

	protected function saveState() {
		$search_form = $this->ui->getWidget('search_form');
		$search_state = $search_form->getDescendantStates();
		$_SESSION[$this->source.'_search_state'] = $search_state;
	}

	protected function loadState() {
		$ret = false;
		$search_form = $this->ui->getWidget('search_form');
		$key = $this->source.'_search_state';

		if (isset($_SESSION[$key])) {
			$search_form->setDescendantStates($_SESSION[$key]);
			//print_r($_SESSION[$key]);
			$ret = true;
		}

		return $ret;
	}

}

?>
