<?php

require_once('Admin/Admin/Confirmation.php');
require_once('SwatDB/SwatDB.php');
require_once('Admin/AdminDependency.php');

/**
 * Delete confirmation page for AdminComponents
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsDelete extends AdminConfirmation {

	public $items = null;

	public function display() {
		$form = $this->ui->getWidget('confirmform');
		$form->addHiddenField('items', $this->items);
		
		$where_items = implode(', ', $this->items);
		
		$dep = new AdminDependency();
		$dep->title = 'component';
		$dep->status_level = AdminDependency::DELETE;

		$dep->entries = AdminDependency::queryDependencyEntries($this->app->db, 'admincomponents',
			'integer:componentid', null, 'text:title', 'displayorder, title', 'componentid in ('.$where_items.')');

		$dep_subcomponents = new AdminDependency();
		$dep_subcomponents->title = 'sub-component';
		$dep_subcomponents->status_level = AdminDependency::NODELETE;
		$dep_subcomponents->display_count = true;

		$dep_subcomponents->entries = AdminDependency::queryDependencyEntries($this->app->db, 'adminsubcomponents',
			'integer:subcomponentid', 'integer:component', 'text:title', 'title', 'component in ('.$where_items.')');

		$dep->addDependency($dep_subcomponents);

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

			$sql = 'delete from admincomponents where componentid in (%s)';
			$items = $form->getHiddenField('items');

			foreach ($items as &$id)
				$id = $this->app->db->quote($id, 'integer');

			$sql = sprintf($sql, implode(',', $items));
			$this->app->db->query($sql);

			$this->app->addMessage(sprintf(_nS('%d component has been deleted.', 
				'%d components have been deleted.', count($items)), count($items)));
		}
	}
}

?>
