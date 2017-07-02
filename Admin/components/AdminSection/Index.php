<?php

/**
 * Index page for admin sections
 *
 * @package   Admin
 * @copyright 2004-2016 silverorange
 */
class AdminAdminSectionIndex extends AdminIndex
{
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(__DIR__.'/index.xml');
	}

	// }}}

	// process phase
	// {{{ protected function processActions()

	protected function processActions(SwatView $view, SwatActions $actions)
	{
		$num = count($view->checked_items);
		$message = null;

		switch ($actions->selected->id) {
		case 'delete':
			$this->app->replacePage('AdminSection/Delete');
			$this->app->getPage()->setItems($view->getSelection());
			break;

		case 'show':
			SwatDB::updateColumn($this->app->db, 'AdminSection',
				'boolean:visible', true, 'id', $view->getSelection());

			$message = new SwatMessage(sprintf(Admin::ngettext(
				'One section has been shown.',
				'%s sections have been shown.', $num),
				SwatString::numberFormat($num)));

			break;

		case 'hide':
			SwatDB::updateColumn($this->app->db, 'AdminSection',
				'boolean:visible', false, 'id', $view->getSelection());

			$message = new SwatMessage(sprintf(Admin::ngettext(
				'One section has been hidden.',
				'%s sections have been hidden.', $num),
				SwatString::numberFormat($num)));

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
		$sql = 'select id, title, visible
			from AdminSection
			order by displayorder';

		$sections = SwatDB::query($this->app->db, $sql, 'AdminSectionWrapper');

		if (count($sections) == 0)
			$this->ui->getWidget('order_tool')->visible = false;

		return $sections;
	}

	// }}}
}

?>
