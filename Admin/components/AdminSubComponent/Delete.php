<?php

require_once 'Admin/pages/AdminDBDelete.php';
require_once 'Admin/pages/AdminConfirmation.php';
require_once 'Admin/AdminListDependency.php';

/**
 * Delete confirmation page for AdminSubComponents
 * @package   Admin
 * @copyright 2005-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminSubComponentDelete extends AdminDBDelete
{
	// {{{ private properties

	private $parent;

	// }}}
	// {{{ public function setParent()

	public function setParent($parent)
	{
		$this->parent = $parent;
	}

	// }}}

	// process phase
	// {{{ protected function processDBData()

	protected function processDBData()
	{
		parent::processDBData();

		$item_list = $this->getItemList('integer');

		$sql = 'delete from AdminSubComponent where id in (%s)';

		$sql = sprintf($sql, $item_list);
		$num = SwatDB::exec($this->app->db, $sql);

		$message = new SwatMessage(sprintf(Admin::ngettext(
			'One sub-component has been deleted.',
			'%s sub-components have been deleted.', $num),
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
		$dep->setTitle(Admin::_('sub-component'), Admin::_('sub-components'));
		$dep->entries = AdminListDependency::queryEntries($this->app->db,
			'AdminSubComponent', 'integer:id', null, 'text:title',
			'displayorder, title', 'id in ('.$item_list.')',
			AdminDependency::DELETE);

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		$message->content_type = 'text/xml';

		if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0)
			$this->switchToCancelButton();

		// rebuild the navbar
		$component_title = SwatDB::queryOneFromTable($this->app->db,
			'AdminComponent', 'text:title', 'id', $this->parent);

		// pop two entries because the AdminDBOrder base class adds an entry
		$this->navbar->popEntries(2);
		$this->navbar->createEntry(Admin::_('Admin Components'),
			'AdminComponent');

		$this->navbar->createEntry($component_title,
			'AdminComponent/Details?id='.$this->parent);

		$this->navbar->createEntry(Admin::_('Delete Sub-Component(s)'));
	}

	// }}}
}

?>
