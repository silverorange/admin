<?php

require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/pages/AdminIndex.php';
require_once 'Swat/SwatTableStore.php';
require_once 'include/HistoryCellRenderer.php';

/**
 * Index page for AdminUsers component
 *
 * @package   Admin
 * @copyright 2005-2009 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminUserIndex extends AdminIndex
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/index.xml');

		// set a default order on the table view
		$index_view = $this->ui->getWidget('index_view');
		$index_view->setDefaultOrderbyColumn(
			$index_view->getColumn('email'),
			SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING);
	}

	// }}}

	// process phase
	// {{{ protected function processActions()

	protected function processActions(SwatTableView $view, SwatActions $actions)
	{
		$num = count($view->checked_items);
		$message = null;

		switch ($actions->selected->id) {
			case 'delete':
				$this->app->replacePage('AdminUser/Delete');
				$this->app->getPage()->setItems($view->getSelection());
				break;

			case 'enable':
				SwatDB::updateColumn($this->app->db, 'AdminUser',
					'boolean:enabled', true, 'id', $view->getSelection());

				$message = new SwatMessage(sprintf(Admin::ngettext(
					"%d user has been enabled.",
					"%d users have been enabled.", $num),
					SwatString::numberFormat($num)));

				break;

			case 'disable':
				SwatDB::updateColumn($this->app->db, 'AdminUser',
					'boolean:enabled', false, 'id',
					$view->getSelection());

				$message = new SwatMessage(sprintf(Admin::ngettext(
					"%d user has been disabled.",
					"%d users have been disabled.", $num),
					SwatString::numberFormat($num)));

				break;
		}

		if ($message !== null)
			$this->app->messages->add($message);
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		// set default time zone
		$date_column =
			$this->ui->getWidget('index_view')->getColumn('last_login');

		$date_renderer = $date_column->getRendererByPosition();
		$date_renderer->display_time_zone = $this->app->default_time_zone;
	}

	// }}}
	// {{{ protected function getTableModel()

	protected function getTableModel(SwatView $view)
	{
		$instance_id = $this->app->getInstanceId();

		$sql = 'select AdminUser.id, AdminUser.email, AdminUser.name,
					AdminUser.enabled, AdminUserLastLoginView.last_login
				from AdminUser
				left outer join AdminUserLastLoginView on
					AdminUserLastLoginView.usernum = AdminUser.id and
					AdminUserLastLoginView.instance %s %s
				order by %s';

		$sql = sprintf($sql,
			SwatDB::equalityOperator($instance_id),
			$this->app->db->quote($instance_id, 'integer'),
			$this->getOrderByClause($view, 'AdminUser.email'));

		$users = SwatDB::query($this->app->db, $sql);

		return $users;
	}

	// }}}
}

?>
