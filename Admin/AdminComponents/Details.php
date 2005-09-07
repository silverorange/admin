<?php

require_once 'Admin/AdminUI.php';
require_once 'Admin/Admin/Index.php';
require_once 'Admin/AdminTableStore.php';
require_once 'SwatDB/SwatDB.php';

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
	}

	public function initDisplay()
	{
		$component_details = $this->ui->getWidget('component_details');

		$fields = array('title', 'shortname', 'show', 'enabled');

		$sql = 'select admincomponents.title as title, shortname,
					adminsections.title as section,
					admincomponents.show as show, enabled
				from admincomponents
					inner join adminsections on
						adminsections.sectionid = admincomponents.section
				where componentid = %s';

		$sql = sprintf($sql, $this->app->db->quote($this->id, 'integer'));

		$rs = SwatDB::queryRowFromTable($this->app->db, $sql);
		echo '<pre>',print_r($row, true),'</pre>';

		if ($row === null)
			return $this->app->replacePageNoAccess();

		$component_details->data = &$row;

		$frame = $this->ui->getWidget('index_frame');
		$frame->title = $row->title;

		foreach ($frame->getChildren('SwatToolLink') as $tool)
			$tool->value = $this->id;

		parent::initDisplay();
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

		if ($store->getRowCount() == 0)
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
