<?php

require_once 'Admin/AdminUI.php';
require_once 'Admin/pages/AdminIndex.php';
require_once 'Admin/AdminTableStore.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Details page for AdminUsers component
 *
 * @package Admin
 * @copyright 2005-2006 silverorange
 */
class AdminUserDetails extends AdminIndex
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/details.xml');

		// set a default order on the table view
		$index_view = $this->ui->getWidget('index_view');
		$index_view->setDefaultOrderbyColumn(
			$index_view->getColumn('login_date'));

		$this->navbar->createEntry(Admin::_('Details'));
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$id = $this->app->initVar('id');

		$row = SwatDB::queryRowFromTable($this->app->db, 'AdminUser',
			array('username','name'), 'id' , $id);

		if ($row === null)
			throw new AdminNotFoundException(
				sprintf(Admin::_("User with id '%s' not found."), $id));

		$frame = $this->ui->getWidget('index_frame');
		$frame->subtitle = $row->name;
	}

	// }}}
	// {{{ protected function getTableStore()

	protected function getTableStore($view)
	{
		$id = $this->app->initVar('id');
	
		$sql = 'select login_date, login_agent, remote_ip
				from AdminUserHistory
				where usernum = %s
				order by %s';

        	$sql = sprintf($sql,
			$this->app->db->quote($id, 'integer'),
			$this->getOrderByClause($view, 'login_date desc'));

		$store = SwatDB::query($this->app->db, $sql, 'AdminTableStore');

		return $store;
	}

	// }}}
}
?>
