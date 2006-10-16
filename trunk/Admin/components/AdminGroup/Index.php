<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/pages/AdminIndex.php';
require_once 'Admin/AdminTableStore.php';

/**
 * Index page for AdminGroups component
 *
 * @package Admin
 * @copyright 2005-2006 silverorange
 */
class AdminAdminGroupIndex extends AdminIndex
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/index.xml');
	}

	// }}}

	// process phase
	// {{{ protected function processActions()

	protected function processActions(SwatTableView $view, SwatActions $actions)
	{
		switch ($actions->selected->id) {
			case 'delete':
				$this->app->replacePage('AdminGroup/Delete');
				$this->app->getPage()->setItems($view->checked_items);
				break;
		}
	}

	// }}}

	// build phase
	// {{{ protected function getTableStore()

	protected function getTableStore($view)
	{
		$view = $this->ui->getWidget('index_view');

		$sql = 'select id, title from AdminGroup order by title';

		$store = SwatDB::query($this->app->db, $sql, 'AdminTableStore');

		return $store;
	}

	// }}}
}

?>
