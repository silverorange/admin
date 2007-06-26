<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Admin/dataobjects/AdminSubComponent.php';

/**
 * Edit page for AdminSubComponents
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 */
class AdminAdminSubComponentEdit extends AdminDBEdit
{
	// {{{ private properties

	private $fields;
	private $parent;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->initSubComponent();

		$this->ui->loadFromXML(dirname(__FILE__).'/edit.xml');

		$this->parent = SiteApplication::initVar('parent');

		$this->fields = array('title', 'shortname', 'boolean:show',
			'integer:component');

		$form = $this->ui->getWidget('edit_form');
		$form->addHiddenField('parent', $this->parent);
	}

	// }}}
	// {{{ protected function initSubComponent()
	protected function initSubComponent()
	{
		$this->subcomponent = new AdminSubComponent();
		$this->subcomponent->setDatabase($this->app->db);

		if ($this->id !== null) {
			if (!$this->subcomponent->load($this->id))
				throw new AdminNotFoundException(
					sprintf(Admin::_('Sub-Component with id "%s" notfound.'),
						$this->id));
		}
	}

	// }}}

	// process phase
	// {{{ protected function validate()

	protected function validate()
	{
		parent::validate();
		$shortname = $this->ui->getWidget('shortname');

		$sql = sprintf('select shortname from AdminSubComponent
				where shortname = %s and id %s %s and component = %s',
			$this->app->db->quote($shortname->value, 'text'),
			SwatDB::equalityOperator($this->id, true),
			$this->app->db->quote($this->id, 'integer'),
			$this->app->db->quote($this->parent, 'integer'));

		$query = SwatDB::queryRow($this->app->db, $sql);

		if ($query !== null) {
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
		$values = $this->ui->getValues(array('title', 'shortname', 'show'));
		$values['component'] = $this->parent;

		$this->subcomponent->title = $values['title'];
		$this->subcomponent->shortname = $values['shortname'];
		$this->subcomponent->show = $values['show'];
		$this->subcomponent->component = $values['component'];
		$this->subcomponent->save();

		$message = new SwatMessage(
			sprintf(Admin::_('Sub-Component “%s” has been saved.'),
			$this->subcomponent->title));

		$this->app->messages->add($message);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->subcomponent));

		$this->parent = intval($this->subcomponent->component);
		$form = $this->ui->getWidget('edit_form');
		$form->addHiddenField('parent', $this->parent);
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		$parent_title = SwatDB::queryOneFromTable($this->app->db,
			'AdminComponent', 'text:title', 'id', $this->parent);

		$this->navbar->popEntry();
		$this->navbar->createEntry('Admin Components', 'AdminComponent');
		$this->navbar->createEntry($parent_title,
			'AdminComponent/Details?id='.$this->parent);

		if ($this->id === null)
			$this->navbar->createEntry('Add Sub-Component');
		else
			$this->navbar->createEntry('Edit Sub-Component');
	}

	// }}}
}

?>
