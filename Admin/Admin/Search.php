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

	protected $ui;
	
	public function display() {
		$form = $this->ui->getWidget('searchform', true);

		if ($form != null)
			$form->action = $this->source;
		
		parent::display();
	}

	public function process() {
		$form = $this->ui->getWidget('searchform', true);

		if ($form != null) {
			if ($form->process()) {
				if (!$form->hasErrorMessage()) {
					$index = $this->ui->getWidget('articlesframe');
					$index->visible = true;
				}
			}
		}

		parent::process();
	}

}
?>
