<?php

require_once 'Admin/AdminUI.php';
require_once 'Admin/pages/AdminIndex.php';
require_once 'Swat/SwatDetailsStore.php';
require_once 'Swat/SwatTableStore.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Index page for AdminComponents
 *
 * @package   Admin
 * @copyright 2005-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminComponentIndex extends AdminIndex
{
	// init phase
	// {{{ protected function initInternal()
	protected function initInternal()
	{
		$this->ui->loadFromXML(__DIR__.'/index.xml');

		$section_flydown = $this->ui->getWidget('section');
		$section_flydown->addOptionsByArray(SwatDB::getOptionArray(
			$this->app->db, 'AdminSection', 'title', 'id', 'displayorder'));
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
			$this->app->replacePage('AdminComponent/Delete');
			$this->app->getPage()->setItems($view->getSelection());
			break;

		case 'show':
			SwatDB::updateColumn($this->app->db, 'AdminComponent',
				'boolean:visible', true, 'id', $view->getSelection());

			$message = new SwatMessage(sprintf(Admin::ngettext(
				'One component has been shown.',
				'%s components have been shown.', $num),
				SwatString::numberFormat($num)));

			break;

		case 'hide':
			SwatDB::updateColumn($this->app->db, 'AdminComponent',
				'boolean:visible', false, 'id', $view->getSelection());

			$message = new SwatMessage(sprintf(Admin::ngettext(
				'One component has been hidden.',
				'%s components have been hidden.', $num),
				SwatString::numberFormat($num)));

			break;

		case 'enable':
			SwatDB::updateColumn($this->app->db, 'AdminComponent',
				'boolean:enabled', true, 'id', $view->getSelection());

			$message = new SwatMessage(sprintf(Admin::ngettext(
				'One component has been enabled.',
				'%s components have been enabled.', $num),
				SwatString::numberFormat($num)));

			break;

		case 'disable':
			SwatDB::updateColumn($this->app->db, 'AdminComponent',
				'boolean:enabled', false, 'id', $view->getSelection());

			$message = new SwatMessage(sprintf(Admin::ngettext(
				'One component has been disabled.',
				'%s components have been disabled.', $num),
				SwatString::numberFormat($num)));

			break;

		case 'change_section':
			$new_section = $actions->selected->widget->value;

			SwatDB::updateColumn($this->app->db, 'AdminComponent',
				'integer:section', $new_section, 'id', $view->getSelection());

			$title = SwatDB::queryOneFromTable($this->app->db, 'AdminSection',
				'text:title', 'id', $new_section);

			$message = new SwatMessage(sprintf(Admin::ngettext(
				'One component has been moved to section “%s”.',
				'%s components have been moved to section “%s”.', $num),
				SwatString::numberFormat($num),
				$title));

			break;
		}

		if ($message !== null)
			$this->app->messages->add($message);
	}

	// }}}

	// build phase
	// {{{ protected function getTableModel()

	protected function getTableModel(SwatView $view)
	{
		/*
		 * Build a custom table-view store here so we can set the sensitivity
		 * on group header change-order links.
		 */

		// get component information
		$sql = 'select AdminComponent.id,
					AdminComponent.title,
					AdminComponent.shortname,
					AdminComponent.section,
					AdminComponent.visible,
					AdminComponent.enabled
				from AdminComponent
				inner join AdminSection
					on AdminSection.id = AdminComponent.section
				order by AdminSection.displayorder, AdminSection.id, %s';

		$sql = sprintf($sql,
			$this->getOrderByClause($view, 'AdminComponent.displayorder,
				AdminComponent.title', 'AdminComponent'));

		$components = SwatDB::query($this->app->db, $sql);

		// get component-count and title for each section
		$sql = 'select section as id, AdminSection.title as title,
			count(AdminComponent.id) as num_components
			from AdminComponent
			inner join AdminSection
				on AdminSection.id = AdminComponent.section
			group by section, AdminSection.id, AdminSection.title,
				AdminSection.displayorder
			order by AdminSection.displayorder, AdminSection.id';

		$section_info = SwatDB::query($this->app->db, $sql);
		$current_section = null;

		$store = new SwatTableStore();

		foreach ($components as $component) {
			$ds = new SwatDetailsStore();

			if ($current_section === null ||
				$current_section->id !== $component->section) {
				foreach ($section_info as $section) {
					if ($section->id === $component->section) {
						$current_section = $section;
						break;
					}
				}
			}

			// set component-specific info
			$ds->id = $component->id;
			$ds->title = $component->title;
			$ds->shortname = $component->shortname;
			$ds->visible = $component->visible;
			$ds->enabled = $component->enabled;

			// set section-specific info
			$ds->section = $current_section->id;
			$ds->section_title = $current_section->title;
			$ds->section_order_sensitive =
				($current_section->num_components > 1);

			$store->add($ds);
		}

		return $store;
	}

	// }}}
}

?>
