<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for AdminComponents
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 */
class AdminComponentEdit extends AdminDBEdit
{
	// {{{ private properties

	private $fields;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML(dirname(__FILE__).'/edit.xml');

		$section_flydown = $this->ui->getWidget('section');
		$section_flydown->addOptionsByArray(SwatDB::getOptionArray(
			$this->app->db, 'AdminSection', 'title', 'id', 'displayorder'));

		$group_list = $this->ui->getWidget('groups');
		$group_list->options = SwatDB::getOptionArray($this->app->db, 
			'AdminGroup', 'title', 'id', 'title');

		$this->fields = array('title', 'shortname', 'integer:section', 
			'boolean:show', 'boolean:enabled', 'description');
	}

	// }}}

	// process phase
	// {{{ protected function validate()

	protected function validate()
	{
		$shortname = $this->ui->getWidget('shortname');

		$query = SwatDB::query($this->app->db, sprintf('select shortname from
			AdminComponent where shortname = %s and id %s %s',
			$this->app->db->quote($shortname->value, 'text'),
			SwatDB::equalityOperator($this->id, true),
			$this->app->db->quote($this->id, 'integer')));

		if (count($query) > 0) {
			$msg = new SwatMessage(
				Admin::_('Shortname already exists and must be unique.'),
				SwatMessage::ERROR);

			$shortname->addMessage($msg);
		}
	}

	// }}}
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array('title', 'shortname', 'section', 
			'show', 'enabled', 'description'));

		if ($this->id === null)
			$this->id = SwatDB::insertRow($this->app->db, 'AdminComponent',
				$this->fields, $values, 'integer:id');
		else
			SwatDB::updateRow($this->app->db, 'AdminComponent', $this->fields,
				$values, 'integer:id', $this->id);

		$group_list = $this->ui->getWidget('groups');

		SwatDB::updateBinding($this->app->db, 'AdminComponentAdminGroupBinding', 
			'component', $this->id, 'groupnum', $group_list->values,
			'AdminGroup', 'id');

		$msg = new SwatMessage(
			sprintf(Admin::_('Component “%s” has been saved.'),
			$values['title']), SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$row = SwatDB::queryRowFromTable($this->app->db, 'AdminComponent', 
			$this->fields, 'integer:id', $this->id);

		if ($row === null)
			throw new AdminNotFoundException(
				sprintf(Admin::_("Component with id '%s' not found."),
				$this->id));

		$this->ui->setValues(get_object_vars($row));

		$group_list = $this->ui->getWidget('groups');
		$group_list->values = SwatDB::queryColumn($this->app->db, 
			'AdminComponentAdminGroupBinding', 'groupnum', 'component', 
			$this->id);
	}

	// }}}
}

?>
