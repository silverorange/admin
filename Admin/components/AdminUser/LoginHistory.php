<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/components/AdminUser/include/HistoryCellRenderer.php';
require_once 'Admin/pages/AdminIndex.php';

/**
 * Login history page for AdminUsers component
 *
 * @package   Admin
 * @copyright 2005-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminUserLoginHistory extends AdminIndex
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(__DIR__.'/login-history.xml');

		// set a default order on the table view
		$index_view = $this->ui->getWidget('index_view');
		$index_view->setDefaultOrderbyColumn(
			$index_view->getColumn('login_date'));

		$this->navbar->createEntry(Admin::_('Login History'));
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		$instance_id = $this->app->getInstanceId();

		$pager = $this->ui->getWidget('pager');
		$sql = 'select count(id) from AdminUserHistory where instance %s %s';
		$pager->total_records = SwatDB::queryOne($this->app->db, sprintf($sql,
			SwatDB::equalityOperator($instance_id),
			$this->app->db->quote($instance_id, 'integer')));

		$pager->link = 'AdminUser/LoginHistory';
		$pager->process();
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		// set default time zone
		$date_column =
			$this->ui->getWidget('index_view')->getColumn('login_date');

		$date_renderer = $date_column->getRendererByPosition();
		$date_renderer->display_time_zone = $this->app->default_time_zone;
	}

	// }}}
	// {{{ protected function getTableModel()

	protected function getTableModel(SwatView $view)
	{
		$pager = $this->ui->getWidget('pager');
		$this->app->db->setLimit($pager->page_size, $pager->current_record);

		$instance_id = $this->app->getInstanceId();

		$sql = 'select usernum, login_date, login_agent, remote_ip, email,
					AdminUser.name
				from AdminUserHistory
				inner join AdminUser on AdminUser.id = AdminUserHistory.usernum
				where instance %s %s
				order by %s';

		$sql = sprintf($sql,
			SwatDB::equalityOperator($instance_id),
			$this->app->db->quote($instance_id, 'integer'),
			$this->getOrderByClause($view, 'login_date desc'));

		$rs = SwatDB::query($this->app->db, $sql);

		return $rs;
	}

	// }}}
}

?>
