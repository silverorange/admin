<?php

require_once 'Admin/Admin/DBDelete.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/AdminDependency.php';

/**
 * Delete confirmation page for AdminSections component
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminSectionsDelete extends AdminDBDelete
{
	public function initDisplay()
	{
		$item_list = $this->getItemList('integer');
		
		$dep = new AdminDependency();
		$dep->title = 'Admin Section';
		$dep->status_level = AdminDependency::DELETE;

		$dep->entries = AdminDependency::queryDependencyEntries($this->app->db, 'adminsections',
			'integer:sectionid', null, 'text:title', 'title', 'sectionid in ('.$item_list.')');

		
		$dep_components = new AdminDependency();
		$dep_components->title = 'component';
		$dep_components->status_level = AdminDependency::DELETE;
		$dep_components->display_count = true;

		$dep_components->entries = AdminDependency::queryDependencyEntries($this->app->db, 'admincomponents',
			'integer:componentid', 'integer:section', 'text:title', 'title', 'section in ('.$item_list.')');

		$dep->addDependency($dep_components);

		
		$dep_subcomponents = new AdminDependency();
		$dep_subcomponents->title = 'sub-component';
		$dep_subcomponents->status_level = AdminDependency::DELETE;
		$dep_subcomponents->display_count = true;

		$dep_subcomponents->entries = AdminDependency::queryDependencyEntries($this->app->db, 'adminsubcomponents',
			'integer:subcomponentid', 'integer:component', 'text:title', 'title',
			'component in (select componentid from admincomponents where section in ('.$item_list.'))');

		$dep_components->addDependency($dep_subcomponents);


		$message = $this->ui->getWidget('confirmation_message');
		$message->content = $dep->getMessage();
		
		parent::initDisplay();
	}

	protected function processDBData()
	{
		parent::processDBData();

		$sql = 'delete from adminsections where sectionid in (%s)';
		$item_list = $this->getItemList('integer');
		$sql = sprintf($sql, $item_list);
		SwatDB::query($this->app->db, $sql);

		$msg = new SwatMessage(sprintf(Admin::ngettext("%d admin section has been deleted.", 
			"%d admin sections have been deleted.", $this->getItemCount()), $this->getItemCount()),
			SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);	
	}
}

?>
