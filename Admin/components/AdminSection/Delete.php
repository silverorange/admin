<?php

require_once 'Admin/pages/AdminDBDelete.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminListDependency.php';
require_once 'Admin/AdminSummaryDependency.php';

/**
 * Delete confirmation page for AdminSections component
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminSectionDelete extends AdminDBDelete
{
	// init phase
	// {{{ protected function processDBData()

	protected function processDBData()
	{
		parent::processDBData();

		$sql = 'delete from AdminSection where id in (%s)';
		$item_list = $this->getItemList('integer');
		$sql = sprintf($sql, $item_list);
		$num = SwatDB::exec($this->app->db, $sql);

		$message = new SwatMessage(sprintf(Admin::ngettext(
			'One admin section has been deleted.',
			'%s admin sections have been deleted.', $num),
			SwatString::numberFormat($num)));

		$this->app->messages->add($message);
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$item_list = $this->getItemList('integer');

		$dep = new AdminListDependency();
		$dep->setTitle(Admin::_('section'), Admin::_('sections'));
		$dep->entries = AdminListDependency::queryEntries($this->app->db,
			'AdminSection', 'integer:id', null, 'text:title', 'title',
			'id in ('.$item_list.')', AdminDependency::DELETE);

		$dep_components = new AdminListDependency();
		$dep_components->setTitle(
			Admin::_('component'), Admin::_('components'));

		$dep_components->entries = AdminListDependency::queryEntries(
			$this->app->db, 'AdminComponent', 'integer:id', 'integer:section',
			'text:title', 'title', 'section in ('.$item_list.')',
			AdminDependency::DELETE);

		$dep->addDependency($dep_components);

		$dep_subcomponents = new AdminSummaryDependency();
		$dep_subcomponents->setTitle(
			Admin::_('sub-component'), Admin::_('sub-components'));

		$dep_subcomponents->summaries = AdminSummaryDependency::querySummaries(
			$this->app->db, 'AdminSubComponent', 'integer:id',
			'integer:component',
			'component in (select id from AdminComponent where section in ('.
			$item_list.'))', AdminDependency::DELETE);

		$dep_components->addDependency($dep_subcomponents);

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		$message->content_type = 'text/xml';
	}

	// }}}
}

?>
