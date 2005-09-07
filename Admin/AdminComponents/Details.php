<?php

require_once 'Admin/AdminUI.php';
require_once 'Admin/Admin/Index.php';
require_once 'Admin/AdminTableStore.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Swat/SwatString.php';

/**
 * Details page for AdminComponents
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsDetails extends AdminIndex
{
	private $id;

	public function init()
	{
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminComponents/details.xml');

		$this->id = intval(SwatApplication::initVar('id'));
		assert($this->id !== null);

		$form = $this->ui->getWidget('index_form');
		$form->addHiddenField('id', $this->id);

		$this->navbar->createEntry(Admin::_('Details'));

		$this->ui->init();
	}

	public function initDisplay()
	{
		$component_details = $this->ui->getWidget('component_details');

		$sql = 'select admincomponents.*,
					adminsections.title as section_title
				from admincomponents
					inner join adminsections on
						adminsections.sectionid = admincomponents.section
				where componentid = %s';

		$sql = sprintf($sql, $this->app->db->quote($this->id, 'integer'));

		$row = SwatDB::queryRow($this->app->db, $sql);

		if ($row === null)
			return $this->app->replacePageNoAccess();

		ob_start();
		$this->displayGroups($this->id);
		$row->groups = ob_get_clean();

		$component_details->data = $row;

		$frame = $this->ui->getWidget('index_frame');
		$frame->title = sprintf(Admin::_("Component: \"%s\""), $row->title);

		foreach ($frame->getDescendants('SwatToolLink') as $tool)
			$tool->value = $this->id;

		$description = $this->ui->getWidget('component_description');

		if (strlen($row->description) == 0)
			$description->parent->visible = false;
		else
			$description->content = SwatString::toXHTML($row->description);
		
		parent::initDisplay();
	}

	private function displayGroups($id)
	{
		$sql = 'select groupid, title
				from admingroups
				where groupid in
					(select groupnum from admincomponent_admingroup 
					where component = %s)';

		$sql = sprintf($sql, $this->app->db->quote($id, 'integer'));
		
		$rs = SwatDB::query($this->app->db, $sql);

		echo '<ul>';

		foreach ($rs as $row) {
			echo '<li>';
			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->href = 'AdminGroups/Details?id='.$row->groupid;
			$anchor_tag->content = $row->title;
			$anchor_tag->display();
			echo '</li>';
		}

		echo '<ul>';
	}

	protected function getTableStore()
	{
		$sql = 'select adminsubcomponents.subcomponentid, 
					adminsubcomponents.title, 
					adminsubcomponents.shortname, 
					adminsubcomponents.show
				from adminsubcomponents 
				where component = %s
				order by adminsubcomponents.displayorder';

		$sql = sprintf($sql, $this->app->db->quote($this->id, 'integer'));

		$store = SwatDB::query($this->app->db, $sql, 'AdminTableStore');

		if ($store->getRowCount() < 2)
			$this->ui->getWidget('order_tool')->visible = false;

		return $store;
	}

	protected function processActions()
	{
		$view = $this->ui->getWidget('index_view');
		$actions = $this->ui->getWidget('index_actions');
		$num = count($view->checked_items);
		$msg = null;

		switch ($actions->selected->id) {
			case 'delete':
				$this->app->replacePage('AdminSubComponents/Delete');
				$this->app->getPage()->setItems($view->checked_items);
				$this->app->getPage()->parent = $this->id;
				break;

			case 'show':
				SwatDB::updateColumn($this->app->db, 'adminsubcomponents', 
					'boolean:show', true, 'subcomponentid', 
					$view->checked_items);
				
				$msg = new SwatMessage(sprintf(Admin::ngettext("%d sub-component has been shown.", 
					"%d sub-components have been shown.", $num), $num));
				
				break;

			case 'hide':
				SwatDB::updateColumn($this->app->db, 'adminsubcomponents', 
					'boolean:show', false, 'subcomponentid', 
					$view->checked_items);
				
				$msg = new SwatMessage(sprintf(Admin::ngettext("%d sub-component has been hidden.", 
					"%d sub-components have been hidden.", $num), $num));
				
				break;
		}
		
		if ($msg !== null)
			$this->app->messages->add($msg);
	}
}

?>
