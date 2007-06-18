<?php

require_once 'Admin/AdminUI.php';
require_once 'Admin/dataobjects/AdminComponent.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Admin/pages/AdminDBEdit.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for AdminComponents
 *
 * @package   Admin
 * @copyright 2005-2007 silverorange
 */
class AdminAdminComponentEdit extends AdminDBEdit
{
	// {{{ private properties

	/**
	 * @var AdminComponent
	 */
	private $component;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->initComponent();

		$this->ui->loadFromXML(dirname(__FILE__).'/edit.xml');

		$section_flydown = $this->ui->getWidget('section');
		$section_flydown->addOptionsByArray(SwatDB::getOptionArray(
			$this->app->db, 'AdminSection', 'title', 'id', 'displayorder'));

		$group_list = $this->ui->getWidget('groups');
		$group_list->options = SwatDB::getOptionArray($this->app->db,
			'AdminGroup', 'title', 'id', 'title');
	}

	// }}}
	// {{{ private function initComponent()

	private function initComponent()
	{
		$this->component = new AdminComponent();
		$this->component->setDatabase($this->app->db);

		if (!$this->component->load($this->id))
			throw new AdminNotFoundException(
				sprintf(Admin::_('Component with id "%s" not found.'),
					$this->id));
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
			$message = new SwatMessage(
				Admin::_('Shortname already exists and must be unique.'),
				SwatMessage::ERROR);

			$shortname->addMessage($message);
		}
	}

	// }}}
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array('title', 'shortname', 'section',
			'show', 'enabled', 'description'));

		$this->component->title = $values['title'];
		$this->component->shortname = $values['shortname'];
		$this->component->section = $values['section'];
		$this->component->show = $values['show'];
		$this->component->enabled = $values['enabled'];
		$this->component->description = $values['description'];
		$this->component->save();

		$group_list = $this->ui->getWidget('groups');

		SwatDB::updateBinding($this->app->db, 'AdminComponentAdminGroupBinding',
			'component', $this->id, 'groupnum', $group_list->values,
			'AdminGroup', 'id');

		$message = new SwatMessage(sprintf(
			Admin::_('Component “%s” has been saved.'), $values['title']),
			SwatMessage::NOTIFICATION);

		$this->app->messages->add($message);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->component));

		$group_list = $this->ui->getWidget('groups');
		$group_list->values = SwatDB::queryColumn($this->app->db,
			'AdminComponentAdminGroupBinding', 'groupnum', 'component',
			$this->id);
	}

	// }}}
}

?>
