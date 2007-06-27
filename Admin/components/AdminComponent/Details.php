<?php

require_once 'Admin/AdminUI.php';
require_once 'Admin/pages/AdminIndex.php';
require_once 'Admin/AdminTableStore.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Swat/SwatString.php';
require_once 'Admin/dataobjects/AdminComponent.php';
require_once 'Admin/dataobjects/AdminSubComponentWrapper.php';
require_once 'Swat/SwatTableStore.php';
require_once 'Swat/SwatDetailsStore.php';

/**
 * Details page for AdminComponents
 *
 * @package Admin
 * @copyright 2005-2006 silverorange
 */
class AdminAdminComponentDetails extends AdminIndex
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

		$this->initComponent();
	}

	// }}}
	// {{{ protected function initComponent()

	protected function initComponent()
	{
		$this->component = new AdminComponent();
		$this->component->setDatabase($this->app->db);
		$this->component->load($this->id);
	}

	// }}}
	// process phase
	// {{{ protected function processActions()

	protected function processActions(SwatTableView $view, SwatActions $actions)
	{
		$num = count($view->checked_items);
		$message = null;

		switch ($actions->selected->id) {
			case 'delete':
				$this->app->replacePage('AdminSubComponent/Delete');
				$this->app->getPage()->setItems($view->checked_items);
				$this->app->getPage()->setParent($this->id);
				break;

			case 'show':
				SwatDB::updateColumn($this->app->db, 'AdminSubComponent',
					'boolean:show', true, 'id', $view->checked_items);

				$message = new SwatMessage(sprintf(Admin::ngettext(
					'One sub-component has been shown.',
					'%d sub-components have been shown.', $num),
					SwatString::numberFormat($num)));

				break;

			case 'hide':
				SwatDB::updateColumn($this->app->db, 'AdminSubComponent',
					'boolean:show', false, 'id', $view->checked_items);

				$message = new SwatMessage(sprintf(Admin::ngettext(
					'One sub-component has been hidden.', 
					'%d sub-components have been hidden.', $num),
					SwatString::numberFormat($num)));

				break;
		}

		if ($message !== null)
			$this->app->messages->add($message);
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

		$ds = new SwatDetailsStore($this->component);

		ob_start();
		$this->displayGroups();
		$ds->groups_summary = ob_get_clean();

		if ($this->component->description !== null)
			$ds->description = SwatString::condense(SwatString::toXHTML(
				$this->component->description));

		$component_details = $this->ui->getWidget('component_details');
		$component_details->data = $ds;

		$frame = $this->ui->getWidget('details_frame');
		$frame->title = Admin::_('Component');
		$frame->subtitle = $this->component->title;
	}

	// }}}
	// {{{ protected function getTableStore()

	protected function getTableStore($view)
	{
		$sub_components = $this->component->sub_components;

		$store = new SwatTableStore();

		foreach ($sub_components as $sub_component)
			$store->addRow($sub_component);
	
		if ($store->getRowCount() < 2)
			$this->ui->getWidget('order_tool')->sensitive = false;

		return $store;
	}

	// }}}
	// {{{ private function displayGroups()

	private function displayGroups()
	{	
		echo '<ul>';

		foreach ($this->component->groups as $group) {
			echo '<li>';
			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->href = 'AdminGroup/Edit?id='.$group->id;
			$anchor_tag->setContent($group->title);
			$anchor_tag->display();
			echo '</li>';
		}

		echo '<ul>';
	}

	// }}}
}

?>
