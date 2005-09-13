<?php

require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/Admin/Index.php';
require_once 'Admin/AdminTableStore.php';

/**
 * Index page for AdminSections
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminSectionsIndex extends AdminIndex
{
	public function processActions()
	{
		$view = $this->ui->getWidget('index_view');
		$actions = $this->ui->getWidget('index_actions');

		$num = count($view->checked_items);
		$msg = null;
		
		switch ($actions->selected->id) {
			case 'delete':
				$this->app->replacePage('AdminSections/Delete');
				$this->app->getPage()->setItems($view->checked_items);
				break;

			case 'show':
				SwatDB::updateColumn($this->app->db, 'adminsections', 
					'boolean:show', true, 'id', 
					$view->checked_items);

				$msg = new SwatMessage(sprintf(Admin::ngettext("%d section has been shown.", 
					"%d sections have been shown.", $num), $num));

				break;

			case 'hide':
				SwatDB::updateColumn($this->app->db, 'adminsections', 
					'boolean:show', false, 'id', 
					$view->checked_items);

				$msg = new SwatMessage(sprintf(Admin::ngettext("%d section has been hidden.", 
					"%d sections have been hidden.", $num), $num));

				break;
		}
		
		if ($msg !== null)
			$this->app->messages->add($msg);
	}

	protected function initInternal()
	{
		$this->ui->loadFromXML('Admin/AdminSections/index.xml');
	}

	protected function getTableStore()
	{
		$view = $this->ui->getWidget('index_view');

		$sql = 'select id, title, show 
				from adminsections 
				order by displayorder';

		$store = SwatDB::query($this->app->db, $sql, 'AdminTableStore');

		if ($store->getRowCount() == 0)
			$this->ui->getWidget('order_tool')->visible = false;


		return $store;
	}
}

?>
