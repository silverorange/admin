<?php

require_once 'Admin/pages/AdminDBDelete.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminListDependency.php';
require_once 'Admin/AdminSummaryDependency.php';

/**
 * Delete confirmation page for AdminComponents
 *
 * @package Admin
 * @copyright 2005-2006 silverorange
 */
class AdminComponentDelete extends AdminDBDelete
{
	// process phase
	// {{{ protected function processDBData()

	protected function processDBData()
	{
		parent::processDBData();

		$sql = 'delete from AdminComponent where id in (%s)';
		$item_list = $this->getItemList('integer');
		$sql = sprintf($sql, $item_list);
		$num = SwatDB::exec($this->app->db, $sql);

		$msg = new SwatMessage(sprintf(Admin::ngettext(
			"%d component has been deleted.", 
			"%d components have been deleted.", $num), $num),
			SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$item_list = $this->getItemList('integer');
		
		$dep = new AdminListDependency();
		$dep->title = 'component';
		$dep->entries = AdminListDependency::queryEntries($this->app->db,
			'AdminComponent', 'integer:id', null, 'text:title',
			'displayorder, title', 'id in ('.$item_list.')',
			AdminDependency::DELETE);

		$dep_subcomponents = new AdminSummaryDependency();
		$dep_subcomponents->title = 'sub-component';
		$dep_subcomponents->summaries = AdminSummaryDependency::querySummaries(
			$this->app->db, 'AdminSubComponent', 'integer:id',
			'integer:component', 'component in ('.$item_list.')',
			AdminDependency::DELETE);

		$dep->addDependency($dep_subcomponents);

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		$message->content_type = 'text/xml';

		if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0)
			$this->switchToCancelButton();
	}

	// }}}
}

?>
