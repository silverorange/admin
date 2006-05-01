<?php

require_once 'Admin/AdminUI.php';
require_once 'Admin/pages/AdminIndex.php';
require_once 'Admin/AdminTableStore.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Swat/SwatString.php';

/**
 * Details page for AdminComponents
 *
 * @package Admin
 * @copyright 2004-2006 silverorange
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

		$this->id = intval(SiteApplication::initVar('id'));
	}

	// }}}

	// process phase
	// {{{ protected function processActions()

	protected function processActions(SwatTableView $view, SwatActions $actions)
	{
		$num = count($view->checked_items);
		$msg = null;

		switch ($actions->selected->id) {
			case 'delete':
				$this->app->replacePage('AdminSubComponents/Delete');
				$this->app->getPage()->setItems($view->checked_items);
				$this->app->getPage()->setParent($this->id);
				break;

			case 'show':
				SwatDB::updateColumn($this->app->db, 'AdminSubComponent', 
					'boolean:show', true, 'id', $view->checked_items);

				$msg = new SwatMessage(sprintf(Admin::ngettext(
					"%d sub-component has been shown.", 
					"%d sub-components have been shown.", $num), $num));

				break;

			case 'hide':
				SwatDB::updateColumn($this->app->db, 'AdminSubComponent', 
					'boolean:show', false, 'id', $view->checked_items);

				$msg = new SwatMessage(sprintf(Admin::ngettext(
					"%d sub-component has been hidden.", 
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

		$this->ui->getWidget('details_toolbar')->setToolLinkValues($this->id);
		$this->ui->getWidget('sub_components_toolbar')->setToolLinkValues(
			$this->id);

		$form = $this->ui->getWidget('index_form');
		$form->addHiddenField('id', $this->id);

		$this->navbar->createEntry(Admin::_('Details'));

		$component_details = $this->ui->getWidget('component_details');

		$sql = 'select AdminComponent.*,
					AdminSection.title as section_title
				from AdminComponent
					inner join AdminSection on
						AdminSection.id = AdminComponent.section
				where AdminComponent.id = %s';

		$sql = sprintf($sql, $this->app->db->quote($this->id, 'integer'));

		$row = SwatDB::queryRow($this->app->db, $sql);

		if ($row === null)
			throw new AdminNotFoundException(
				sprintf(Admin::_("Component with id '%s' not found."), 
					$this->id));

		ob_start();
		$this->displayGroups($this->id);
		$row->groups = ob_get_clean();

		if ($row->description !== null)
			$row->description = SwatString::condense(SwatString::toXHTML(
				$row->description));

		$component_details->data = $row;

		$frame = $this->ui->getWidget('details_frame');
		$frame->title = Admin::_('Component: ');
		$frame->subtitle = $row->title;
	}

	// }}}
	// {{{ protected function getTableStore()

	protected function getTableStore($view)
	{
		$sql = 'select AdminSubComponent.id, 
					AdminSubComponent.title, 
					AdminSubComponent.shortname, 
					AdminSubComponent.show
				from AdminSubComponent 
				where component = %s
				order by AdminSubComponent.displayorder';

		$sql = sprintf($sql, $this->app->db->quote($this->id, 'integer'));

		$store = SwatDB::query($this->app->db, $sql, 'AdminTableStore');

		if ($store->getRowCount() < 2)
			$this->ui->getWidget('order_tool')->sensitive = false;

		return $store;
	}

	// }}}
	// {{{ private function displayGroups()

	private function displayGroups($id)
	{
		$sql = 'select id, title
				from AdminGroup
				where id in
					(select groupnum from AdminComponentAdminGroupBinding 
					where component = %s)';

		$sql = sprintf($sql, $this->app->db->quote($id, 'integer'));
		
		$rs = SwatDB::query($this->app->db, $sql);

		echo '<ul>';

		foreach ($rs as $row) {
			echo '<li>';
			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->href = 'AdminGroups/Edit?id='.$row->id;
			$anchor_tag->setContent($row->title);
			$anchor_tag->display();
			echo '</li>';
		}

		echo '<ul>';
	}

	// }}}
}

?>
