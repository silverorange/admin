<?php

require_once('Admin/Admin/Confirmation.php');
require_once('SwatDB/SwatDB.php');
require_once('Admin/AdminDependency.php');

/**
 * Delete confirmation page for AdminSubComponents
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminSubComponentsDelete extends AdminConfirmation {

	public $items = null;

	public function display() {
		$form = $this->ui->getWidget('confirmform');
		$form->addHiddenField('items', $this->items);
		
		$where_items = implode(', ', $this->items);
		
		$dep = new AdminDependency();
		$dep->title = 'subcomponent';

		$dep->entries = AdminDependency::queryDependencyEntries($this->app->db, 'adminsubcomponents',
			'integer:subcomponentid', null, 'text:title', 'displayorder, title', 
			'subcomponentid in ('.$where_items.')');

		$message = $this->ui->getWidget('message');
		$message->content = $dep->getMessage();
		
		if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0) {
			$btn_yes = $this->ui->getWidget('btn_yes');
			$btn_yes->visible = false;

			$btn_no = $this->ui->getWidget('btn_no');
			$btn_no->title = _S("Cancel");
		}

		parent::display();
	}

	protected function processResponse() {
		$form = $this->ui->getWidget('confirmform');

		if ($form->button->name == 'btn_yes') {

			$sql = 'delete from adminsubcomponents where subcomponentid in (%s)';
			$items = $form->getHiddenField('items');

			foreach ($items as &$id)
				$id = $this->app->db->quote($id, 'integer');

			$sql = sprintf($sql, implode(',', $items));
			$this->app->db->query($sql);

			$msg = new SwatMessage(sprintf(_nS("%d sub-component has been deleted.", 
				"%d sub-components have been deleted.", count($items)), count($items)), SwatMessage::INFO);

			$this->app->addMessage($msg);
		}
	}
}

?>
