<?php

require_once 'Admin/AdminUI.php';
require_once 'Admin/pages/AdminIndex.php';
require_once 'Admin/AdminTableStore.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Index page for AdminComponents
 *
 * @package Admin
 * @copyright 2004-2006 silverorange
 */
class AdminComponentsIndex extends AdminIndex
{
	// init phase
	// {{{ protected function initInternal()
	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/index.xml');

		$section_flydown = $this->ui->getWidget('section');
		$section_flydown->addOptionsByArray(SwatDB::getOptionArray(
			$this->app->db, 'AdminSections', 'title', 'id', 'displayorder'));
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
			$this->app->replacePage('AdminComponents/Delete');
			$this->app->getPage()->setItems($view->checked_items);
			break;

		case 'show':
			SwatDB::updateColumn($this->app->db, 'AdminComponent', 
				'boolean:show', true, 'id', $view->checked_items);

			$msg = new SwatMessage(sprintf(Admin::ngettext(
				"%d component has been shown.", 
				"%d components have been shown.", $num), $num));

			break;

		case 'hide':
			SwatDB::updateColumn($this->app->db, 'AdminComponent', 
				'boolean:show', false, 'id', $view->checked_items);

			$msg = new SwatMessage(sprintf(Admin::ngettext(
				"%d component has been hidden.", 
				"%d components have been hidden.", $num), $num));

			break;

		case 'enable':
			SwatDB::updateColumn($this->app->db, 'AdminComponent', 
				'boolean:enabled', true, 'id', $view->checked_items);

			$msg = new SwatMessage(sprintf(Admin::ngettext(
				"%d component has been enabled.", 
				"%d components have been enabled.", $num), $num));

			break;

		case 'disable':
			SwatDB::updateColumn($this->app->db, 'AdminComponent', 
				'boolean:enabled', false, 'id', $view->checked_items);

			$msg = new SwatMessage(sprintf(Admin::ngettext(
				"%d component has been disabled.", 
				"%d components have been disabled.", $num), $num));

			break;

		case 'change_section':
			$new_section = $actions->selected->widget->value;

			SwatDB::updateColumn($this->app->db, 'AdminComponent', 
				'integer:section', $new_section, 'id', $view->checked_items);

			$title = SwatDB::queryOneFromTable($this->app->db, 'AdminSection', 
				'text:title', 'id', $new_section);

			$msg = new SwatMessage(sprintf(Admin::ngettext(
				"%d component has been moved to section \"%s\".", 
				"%d components have been moved to section \"%s\".", $num), $num,
				$title));

			break;
		}

		if ($msg !== null)
			$this->app->messages->add($msg);
	}

	// }}}

	// build phase
	// {{{ protected function getTableStore()

	protected function getTableStore($view)
	{
		$sql = 'select AdminComponent.id,
					AdminComponent.title, 
					AdminComponent.shortname, 
					AdminComponent.section, 
					AdminComponent.show,
					AdminComponent.enabled,
					AdminSection.title as section_title
				from AdminComponent 
				inner join AdminSection 
					on AdminSection.id = AdminComponent.section
				order by AdminSection.displayorder, AdminSection.id, %s';

		$sql = sprintf($sql,
			$this->getOrderByClause($view, 'AdminComponent.displayorder, 
				AdminComponent.title', 'AdminComponent'));

		$store = SwatDB::query($this->app->db, $sql, 'AdminTableStore');

		return $store;
	}

	// }}}
}

?>
