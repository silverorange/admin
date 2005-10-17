<?php

require_once 'Admin/AdminUI.php';
require_once 'Admin/pages/AdminIndex.php';
require_once 'Admin/AdminTableStore.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Swat/SwatString.php';

/**
 * Details page for AdminComponents
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsDetails extends AdminIndex
{
	// {{{ private properties

	private $id;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/details.xml');

		$this->id = intval(SwatApplication::initVar('id'));
	}

	// }}}

	// process phase
	// {{{ protected function processActions()

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
				$this->app->getPage()->setParent($this->id);
				break;

			case 'show':
				SwatDB::updateColumn($this->app->db, 'adminsubcomponents', 
					'boolean:show', true, 'id', 
					$view->checked_items);

				$msg = new SwatMessage(sprintf(Admin::ngettext("%d sub-component has been shown.", 
					"%d sub-components have been shown.", $num), $num));

				break;

			case 'hide':
				SwatDB::updateColumn($this->app->db, 'adminsubcomponents', 
					'boolean:show', false, 'id', 
					$view->checked_items);

				$msg = new SwatMessage(sprintf(Admin::ngettext("%d sub-component has been hidden.", 
					"%d sub-components have been hidden.", $num), $num));

				break;
		}

		if ($msg !== null)
			$this->app->messages->add($msg);
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		$form = $this->ui->getWidget('index_form');
		$form->addHiddenField('id', $this->id);

		$this->navbar->createEntry(Admin::_('Details'));

		$component_details = $this->ui->getWidget('component_details');

		$sql = 'select admincomponents.*,
					adminsections.title as section_title
				from admincomponents
					inner join adminsections on
						adminsections.id = admincomponents.section
				where admincomponents.id = %s';

		$sql = sprintf($sql, $this->app->db->quote($this->id, 'integer'));

		$row = SwatDB::queryRow($this->app->db, $sql);

		if ($row === null)
			return $this->app->replacePageNoAccess(
				new SwatMessage(sprintf(Admin::_("Component with id '%s' ".
					'not found.'), $this->id), SwatMessage::ERROR));

		ob_start();
		$this->displayGroups($this->id);
		$row->groups = ob_get_clean();

		$row->description = SwatString::condense(SwatString::toXHTML($row->description));

		$component_details->data = $row;

		$frame = $this->ui->getWidget('index_frame');
		$frame->title = sprintf(Admin::_('Component: <span>%s</span>'),
			$row->title);

		foreach ($frame->getDescendants('SwatToolLink') as $tool)
			$tool->value = $this->id;
	}

	// }}}
	// {{{ protected function getTableStore()

	protected function getTableStore($view)
	{
		$sql = 'select adminsubcomponents.id, 
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

	// }}}
	// {{{ private function displayGroups()

	private function displayGroups($id)
	{
		$sql = 'select id, title
				from admingroups
				where id in
					(select groupnum from admincomponent_admingroup 
					where component = %s)';

		$sql = sprintf($sql, $this->app->db->quote($id, 'integer'));
		
		$rs = SwatDB::query($this->app->db, $sql);

		echo '<ul>';

		foreach ($rs as $row) {
			echo '<li>';
			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->href = 'AdminGroups/Details?id='.$row->id;
			$anchor_tag->content = $row->title;
			$anchor_tag->display();
			echo '</li>';
		}

		echo '<ul>';
	}

	// }}}
}

?>
