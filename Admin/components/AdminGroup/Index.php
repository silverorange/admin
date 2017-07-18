<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/pages/AdminIndex.php';
require_once 'Swat/SwatTableStore.php';

/**
 * Index page for AdminGroups component
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminGroupIndex extends AdminIndex
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(__DIR__.'/index.xml');
	}

	// }}}

	// process phase
	// {{{ protected function processActions()

	protected function processActions(SwatView $view, SwatActions $actions)
	{
		switch ($actions->selected->id) {
			case 'delete':
				$this->app->replacePage('AdminGroup/Delete');
				$this->app->getPage()->setItems($view->getSelection());
				break;
		}
	}

	// }}}

	// build phase
	// {{{ protected function getTableModel()

	protected function getTableModel(SwatView $view)
	{
		$sql = 'select id, title from AdminGroup order by title';
		$groups = SwatDB::query($this->app->db, $sql, 'AdminGroupWrapper');

		return $groups;
	}

	// }}}
}

?>
