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

		$where_clause = 'componentid in ('.implode(', ', $this->items).')';
		$components = SwatDB::getOptionArray($this->app->db, 'admincomponents',
			'text:title', 'integer:componentid', 'displayorder, title', $where_clause);

		$message = $this->ui->getWidget('message');

		//$dep = new AdminDependency($components, AdminDependency::DELETE, _S("components"));
		//$message->content .= $dep->getMessage(false);

		$message->content = '<p>'._("The following components will be deleted:").'</p>';
		$message->content .= '<ul>';

		foreach ($components as $id => $title) {
			$message->content .= '<li>'.$title;

			$subcomponents = SwatDB::getOptionArray($this->app->db, 'adminsubcomponents',
				'text:title', 'integer:subcomponentid', 'title', 'component = '.$id);

			$dep = new AdminDependency($subcomponents, AdminDependency::DELETE, _S("sub-components"));
			$message->content .= $dep->getMessage();

			$message->content .= '</li>';
		}

		$message->content .= '</ul>';

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

			$this->app->addMessage(sprintf(_nS('%d admin component has been deleted.', 
				'%d admin components have been deleted.', count($items)), count($items)));
		}
	}
}

?>
