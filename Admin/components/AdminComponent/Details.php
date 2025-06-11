<?php

/**
 * Details page for AdminComponents
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminComponentDetails extends AdminIndex
{


	private $id;

	/**
	 * @var AdminComponent
	 */
	private $details_component;



	// init phase


	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML(__DIR__.'/details.xml');

		$this->id = intval(SiteApplication::initVar('id'));

		$this->initComponent();
	}




	protected function initComponent()
	{
		$class_name = SwatDBClassMap::get('AdminComponent');
		$this->details_component = new $class_name();
		$this->details_component->setDatabase($this->app->db);

		if (!$this->details_component->load($this->id)) {
			throw new AdminNotFoundException(
				sprintf(Admin::_('Component with id "%s" not found.'),
					$this->id));
		}
	}



	// process phase


	protected function processActions(SwatView $view, SwatActions $actions)
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
				'boolean:visible', true, 'id', $view->checked_items);

			$message = new SwatMessage(sprintf(Admin::ngettext(
				'One sub-component has been shown.',
				'%s sub-components have been shown.', $num),
				SwatString::numberFormat($num)));

			break;

		case 'hide':
			SwatDB::updateColumn($this->app->db, 'AdminSubComponent',
				'boolean:visible', false, 'id', $view->checked_items);

			$message = new SwatMessage(sprintf(Admin::ngettext(
				'One sub-component has been hidden.',
				'%s sub-components have been hidden.', $num),
				SwatString::numberFormat($num)));

			break;
		}

		if ($message !== null)
			$this->app->messages->add($message);
	}



	// build phase


	protected function buildInternal()
	{
		parent::buildInternal();

		$this->ui->getWidget('details_toolbar')->setToolLinkValues($this->id);
		$this->ui->getWidget('sub_components_toolbar')->setToolLinkValues(
			$this->id);

		$form = $this->ui->getWidget('index_form');
		$form->addHiddenField('id', $this->id);

		$this->navbar->createEntry(Admin::_('Details'));

		$ds = new SwatDetailsStore($this->details_component);

		ob_start();
		$this->displayGroups();
		$ds->groups_summary = ob_get_clean();

		if ($this->details_component->description !== null)
			$ds->description = SwatString::condense(SwatString::toXHTML(
				$this->details_component->description));

		$component_details = $this->ui->getWidget('component_details');
		$component_details->data = $ds;

		$frame = $this->ui->getWidget('details_frame');
		$frame->title = Admin::_('Component');
		$frame->subtitle = $this->details_component->title;
	}




	protected function getTableModel(SwatView $view): ?SwatTableModel
	{
		$sub_components = $this->details_component->sub_components;

		if (count($sub_components) < 2)
			$this->ui->getWidget('order_tool')->sensitive = false;

		return $sub_components;
	}




	private function displayGroups()
	{
		echo '<ul>';

		foreach ($this->details_component->groups as $group) {
			echo '<li>';
			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->href = 'AdminGroup/Edit?id='.$group->id;
			$anchor_tag->setContent($group->title);
			$anchor_tag->display();
			echo '</li>';
		}

		echo '<ul>';
	}


}

?>
