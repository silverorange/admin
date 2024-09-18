<?php

/**
 * Index page for AdminUsers component
 *
 * @package   Admin
 * @copyright 2005-2022 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminUserIndex extends AdminIndex
{
	// init phase


	protected function initInternal()
	{
		$this->ui->loadFromXML(__DIR__.'/index.xml');

		// set a default order on the table view
		$index_view = $this->ui->getWidget('index_view');
		$index_view->setDefaultOrderbyColumn(
			$index_view->getColumn('email'),
			SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING);
	}


	// process phase


	protected function processActions(SwatView $view, SwatActions $actions)
	{
		$num = count($view->checked_items);
		$message = null;

		switch ($actions->selected->id) {
		case 'reactivate':
			$this->app->replacePage('AdminUser/Reactivate');
			$this->app->getPage()->setItems($view->getSelection());
			break;

		case 'delete':
			$this->app->replacePage('AdminUser/Delete');
			$this->app->getPage()->setItems($view->getSelection());
			break;

		case 'enable':
			SwatDB::updateColumn($this->app->db, 'AdminUser',
				'boolean:enabled', true, 'id', $view->getSelection());

			$message = new SwatMessage(sprintf(Admin::ngettext(
				'One user has been enabled.',
				'%s users have been enabled.', $num),
				SwatString::numberFormat($num)));

			break;

		case 'disable':
			SwatDB::updateColumn($this->app->db, 'AdminUser',
				'boolean:enabled', false, 'id',
				$view->getSelection());

			$message = new SwatMessage(sprintf(Admin::ngettext(
				'One user has been disabled.',
				'%s users have been disabled.', $num),
				SwatString::numberFormat($num)));

			break;
		}

		if ($message !== null)
			$this->app->messages->add($message);
	}


	// build phase


	protected function buildInternal()
	{
		parent::buildInternal();

		// set default time zone
		$date_column =
			$this->ui->getWidget('index_view')->getColumn('last_login');

		$date_renderer = $date_column->getRendererByPosition();
		$date_renderer->display_time_zone = $this->app->default_time_zone;

		$this->ui->getWidget('index_view')->getColumn('two_fa')->visible =
			$this->app->is2FaEnabled();
	}



	protected function buildActiveNote()
	{
		$locale = SwatI18NLocale::get();

		$class_name = SwatDBClassMap::get('AdminUser');

		$note = $this->ui->getWidget('active_note');
		$note->visible = true;
		$note->content = sprintf(
			Admin::_(
				'Users become inactive after %s days of inactivity. To '.
				'reactivate a user, select the user and choose '.
				'“reactivate…” from the menu below.'
			),
			$locale->formatNumber($class_name::EXPIRY_DAYS)
		);
	}



	protected function getTableModel(SwatView $view)
	{
		$instance_id = $this->app->getInstanceId();

		$sql = sprintf(
			'select AdminUser.id, AdminUser.email, AdminUser.name,
					AdminUser.activation_date, AdminUser.enabled,
					AdminUserLastLoginView.last_login,
					AdminUser.two_fa_enabled
				from AdminUser
				left outer join AdminUserLastLoginView on
					AdminUserLastLoginView.usernum = AdminUser.id and
					AdminUserLastLoginView.instance %s %s
				order by %s',
			SwatDB::equalityOperator($instance_id),
			$this->app->db->quote($instance_id, 'integer'),
			$this->getOrderByClause($view, 'AdminUser.email')
		);

		$rows = SwatDB::query($this->app->db, $sql);
		$active_users = [];
		$inactive_users = [];

		// Build row objects and separate based on active/inactive status.
		foreach ($rows as $row) {
			if ($row->activation_date !== null) {
				$row->activation_date = new SwatDate($row->activation_date);
			}

			if ($row->last_login !== null) {
				$row->last_login = new SwatDate($row->last_login);
			}

			$class_name = SwatDBClassMap::get('AdminUser');

			$ds = new SwatDetailsStore($row);
			$user = new $class_name($row);
			$user->setDatabase($this->app->db);
			if ($row->last_login instanceof SwatDate) {
				$user->most_recent_history = new AdminUserHistory();
				$user->most_recent_history->login_date = $row->last_login;
			}

			$ds->is_active = $user->isActive();
			if ($ds->is_active) {
				$ds->active_title = Admin::_('Active');
				$active_users[] = $ds;
			} else {
				$ds->active_title = Admin::_('Inactive');
				$inactive_users[] = $ds;
			}
		}

		// Build the resulting table store sorted by active/inactive.
		$store = new SwatTableStore();

		foreach ($active_users as $ds) {
			$store->add($ds);
		}

		foreach ($inactive_users as $ds) {
			$store->add($ds);
		}

		// If there are inactive users, show a note with help text.
		if (count($inactive_users) > 0) {
			$this->buildActiveNote();
		}

		return $store;
	}


	// finalize phase


	public function finalize()
	{
		parent::finalize();
		$this->layout->addHtmlHeadEntry(
			'packages/admin/styles/admin-user-index-page.css'
		);
	}

}

?>
