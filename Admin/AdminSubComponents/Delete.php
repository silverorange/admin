<?php

require_once 'Admin/Admin/DBDelete.php';
require_once 'Admin/Admin/Confirmation.php';
require_once 'Admin/AdminDependency.php';

/**
 * Delete confirmation page for AdminSubComponents
 * @package Admin
 * @copyright silverorange 2005
 */
class AdminSubComponentsDelete extends AdminDBDelete
{
	public $parent;

	public function initDisplay()
	{
		$item_list = $this->getItemList('integer');
		
		$dep = new AdminDependency();
		$dep->title = Admin::_('Sub-Component');
		$dep->status_level = AdminDependency::DELETE;

		$dep->entries = AdminDependency::queryDependencyEntries($this->app->db, 'adminsubcomponents',
			'integer:subcomponentid', null, 'text:title', 'displayorder, title', 
			'subcomponentid in ('.$item_list.')');

		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		
		if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0)
			$this->displayCancelButton();

		parent::initDisplay();

		//rebuild the navbar
		$component_title = SwatDB::queryOneFromTable($this->app->db, 'admincomponents', 'text:title',
			'componentid', $this->parent);

		$this->navbar->popEntry();
		$this->navbar->createEntry(Swat::_("Admin Components"), 'AdminComponents');
		$this->navbar->createEntry($component_title, 'AdminComponents/Details?id='.$this->parent);
	}

	protected function processDBData()
	{
		parent::processDBData();
		
		$item_list = $this->getItemList('integer');
		
		$sql = 'delete from adminsubcomponents where subcomponentid in (%s)';

		$sql = sprintf($sql, $item_list);
		$this->app->db->query($sql);

		$msg = new SwatMessage(sprintf(Swat::ngettext("%d sub-component has been deleted.", 
			"%d sub-components have been deleted.", count($item_list)), count($item_list)), SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);
	}
}

?>
