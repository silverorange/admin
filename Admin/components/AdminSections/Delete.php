<?php

require_once 'Admin/pages/AdminDBDelete.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminListDependency.php';
require_once 'Admin/AdminSummaryDependency.php';

/**
 * Delete confirmation page for AdminSections component
 *
 * @package   Admin
 * @copyright 2004-2006 silverorange
 */
class AdminSectionsDelete extends AdminDBDelete
{
	// init phase
	// {{{ protected function processDBData()

	protected function processDBData()
	{
		parent::processDBData();

		$sql = 'delete from adminsections where id in (%s)';
		$item_list = $this->getItemList('integer');
		$sql = sprintf($sql, $item_list);
		$num = SwatDB::exec($this->app->db, $sql);

		$msg = new SwatMessage(sprintf(Admin::ngettext(
			"%d admin section has been deleted.", 
			"%d admin sections have been deleted.", $num), $num),
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
		$dep->title = 'Admin Section';
		$dep->default_status_level = AdminDependency::DELETE;
		$dep->entries = AdminDependency::queryDependencyEntries($this->app->db,
			'adminsections', 'integer:id', null, 'text:title', 'title',
			'id in ('.$item_list.')');

		$dep_components = new AdminSummaryDependency();
		$dep_components->title = 'component';
		$dep_components->default_status_level = AdminDependency::DELETE;
		$dep_components->entries = AdminDependency::queryDependencyEntries($this->app->db,
			'admincomponents', 'integer:id', 'integer:section', 'text:title',
			'title', 'section in ('.$item_list.')');

		$dep->addDependency($dep_components);

		$dep_subcomponents = new AdminSummaryDependency();
		$dep_subcomponents->title = 'sub-component';
		$dep_subcomponents->default_status_level = AdminDependency::DELETE;
		$dep_subcomponents->entries = AdminDependency::queryDependencyEntries($this->app->db,
			'adminsubcomponents', 'integer:id', 'integer:component',
			'text:title', 'title',
			'component in (select id from admincomponents where section in ('.$item_list.'))');

		$dep_components->addDependency($dep_subcomponents);

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		$message->content_type = 'text/xml';
	}

	// }}}
}

?>
