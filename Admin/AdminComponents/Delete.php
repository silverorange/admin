<?php

require_once('Admin/Admin/DBDelete.php');
require_once('SwatDB/SwatDB.php');
require_once('Admin/AdminDependency.php');

/**
 * Delete confirmation page for AdminComponents
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsDelete extends AdminDBDelete {

	public $items = null;

	public function display() {
		$form = $this->ui->getWidget('confirmation_form');
		$form->addHiddenField('items', $this->items);

		foreach ($items as &$id)
			$id = $this->app->db->quote($id, 'integer');

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

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		
		if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0) {
			$this->ui->getWidget('yes_button')->visible = false;
			$this->ui->getWidget('no_button')->title = _S("Cancel");
		}

		parent::display();
	}

	protected function deleteDBData() {
		$form = $this->ui->getWidget('confirmation_form');
		
		$sql = 'delete from admincomponents where componentid in (%s)';
		$items = $form->getHiddenField('items');

		foreach ($items as &$id)
			$id = $this->app->db->quote($id, 'integer');

		$sql = sprintf($sql, implode(',', $items));
		//$this->app->db->query($sql);
		SwatDB::query($this->app->db, $sql);

		$msg = new SwatMessage(sprintf(_nS("%d component has been deleted.", 
			"%d components have been deleted.", count($items)), count($items)), SwatMessage::INFO);

		$this->app->addMessage($msg);	
	}
}

?>
