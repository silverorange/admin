<?php

require_once('Admin/Admin/Confirmation.php');
require_once('SwatDB/SwatDB.php');

/**
 * Delete confirmation page for AdminComponents
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsDelete extends AdminConfirmation {

	public $items = null;

	protected function displayMessage() {
		$form = $this->ui->getWidget('confirmform');
		$form->addHiddenField('items', $this->items);

		$where_clause = 'componentid in ('.implode(', ', $this->items).')';
		$items = SwatDB::getOptionArray($this->app->db, 'admincomponents',
			'text:title', 'integer:componentid', '', $where_clause);

		$message = $this->ui->getWidget('message');
		$message->content = '<ul><li>';
		$message->content .= implode('</li><li>', $items);
		$message->content .= '</li></ul>';
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
		}
	}
}

?>
