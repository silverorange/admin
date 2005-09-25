<?php

require_once 'Admin/pages/AdminDBDelete.php';
require_once 'Admin/pages/AdminConfirmation.php';
require_once 'Admin/AdminDependency.php';

/**
 * Delete confirmation page for AdminSubComponents
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminSubComponentsDelete extends AdminDBDelete
{
	// {{{ public properties

	// TODO: this looks hacky:
	public $parent;

	// }}}

	// process phase
	// {{{ protected function processDBData()

	protected function processDBData()
	{
		parent::processDBData();

		$item_list = $this->getItemList('integer');

		$sql = 'delete from adminsubcomponents where id in (%s)';

		$sql = sprintf($sql, $item_list);
		SwatDB::query($this->app->db, $sql);

		$msg = new SwatMessage(sprintf(Swat::ngettext("%d sub-component has been deleted.", 
			"%d sub-components have been deleted.", count($item_list)), count($item_list)), SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);
	}

	// }}}

	// build phase
	// {{{ protected function initDisplay()

	protected function initDisplay()
	{
		parent::initDisplay();
		$item_list = $this->getItemList('integer');

		$dep = new AdminDependency();
		$dep->title = Admin::_('Sub-Component');
		$dep->status_level = AdminDependency::DELETE;

		$dep->entries = AdminDependency::queryDependencyEntries($this->app->db, 'adminsubcomponents',
			'integer:id', null, 'text:title', 'displayorder, title', 
			'id in ('.$item_list.')');

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();

		if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0)
			$this->displayCancelButton();

		// rebuild the navbar
		$component_title = SwatDB::queryOneFromTable($this->app->db, 'admincomponents', 'text:title',
			'id', $this->parent);

		// pop two entries because the AdminDBOrder base class adds an entry
		$this->navbar->popEntries(2);
		$this->navbar->createEntry(Swat::_("Admin Components"), 'AdminComponents');
		$this->navbar->createEntry($component_title, 'AdminComponents/Details?id='.$this->parent);
		$this->navbar->createEntry('Delete Sub-Component(s)');
	}
}

?>
