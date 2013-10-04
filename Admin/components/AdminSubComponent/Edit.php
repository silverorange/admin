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
 * @copyright 2005-2009 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminSubComponentEdit extends AdminDBEdit
{
	// {{{ private properties

	private $parent;
	private $edit_subcomponent;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->parent = SiteApplication::initVar('parent');
		$this->initSubComponent();

		$this->ui->loadFromXML(__DIR__.'/edit.xml');

		if ($this->parent === null && $this->edit_subcomponent->id === null)
			throw new AdminNotFoundException(
				Admin::_('Must supply a Component ID for newly created'.
						 ' Sub-Compoenets.'));

		$form = $this->ui->getWidget('edit_form');
		$form->addHiddenField('parent', $this->parent);
	}

	// }}}
	// {{{ protected function initSubComponent()

	protected function initSubComponent()
	{
		$class_name = SwatDBClassMap::get('AdminSubComponent');
		$this->edit_subcomponent = new $class_name();
		$this->edit_subcomponent->setDatabase($this->app->db);

		if ($this->id !== null) {
			if (!$this->edit_subcomponent->load($this->id))
				throw new AdminNotFoundException(
					sprintf(Admin::_('Sub-Component with id "%s" not found.'),
						$this->id));

			$this->parent =
				$this->edit_subcomponent->getInternalValue('component');
		}
	}

	// }}}

	// process phase
	// {{{ protected function validate()

	protected function validate()
	{
		$shortname = $this->ui->getWidget('shortname');

		$class_name = SwatDBClassMap::get('AdminSubComponent');
		$subcomponent = new $class_name();
		$subcomponent->setDatabase($this->app->db);

		if ($subcomponent->loadFromShortname($shortname->value)) {
			if ($subcomponent->id !== $this->edit_subcomponent->id) {
				$message = new SwatMessage(
					Admin::_('Shortname already exists and must be unique.'));

				$shortname->addMessage($message);
			}
		}
	}

	// }}}
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array('title', 'shortname', 'visible'));
		$values['component'] = $this->parent;

		$this->edit_subcomponent->title     = $values['title'];
		$this->edit_subcomponent->shortname = $values['shortname'];
		$this->edit_subcomponent->visible   = $values['visible'];
		$this->edit_subcomponent->component = $values['component'];
		$this->edit_subcomponent->save();

		$message = new SwatMessage(
			sprintf(Admin::_('Sub-Component “%s” has been saved.'),
			$this->edit_subcomponent->title));

		$this->app->messages->add($message);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->edit_subcomponent));
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
