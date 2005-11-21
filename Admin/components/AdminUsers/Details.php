<?php

require_once 'Admin/AdminUI.php';
require_once 'Admin/pages/AdminIndex.php';
require_once 'Admin/AdminTableStore.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Details page for AdminUsers component
 *
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminUsersDetails extends AdminIndex
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/details.xml');

		// set a default order on the table view
		$index_view = $this->ui->getWidget('index_view');
		$index_view->setDefaultOrderbyColumn(
			$index_view->getColumn('logindate'));

		$this->navbar->createEntry(Admin::_('Details'));
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$id = $this->app->initVar('id');

		$row = SwatDB::queryRowFromTable($this->app->db, 'adminusers',
			array('username','name'), 'id' , $id);

		if ($row === null)
			return $this->app->replacePageNoAccess(
				new SwatMessage(sprintf(Admin::_("User with id '%s' ".
					'not found.'), $id), SwatMessage::ERROR));

		$frame = $this->ui->getWidget('index_frame');
		$frame->title.=': <span>'.$row->name.'</span>';
	}

	// }}}
	// {{{ protected function getTableStore()

	protected function getTableStore($view)
	{
		$id = $this->app->initVar('id');
	
		$sql = 'select logindate, loginagent, remoteip
				from adminuserhistory
				where usernum = %s
				order by %s';

        	$sql = sprintf($sql,
			$this->app->db->quote($id, 'integer'),
			$this->getOrderByClause($view, 'logindate desc'));

		$store = SwatDB::query($this->app->db, $sql, 'AdminTableStore');

		return $store;
	}

	// }}}
}
?>
