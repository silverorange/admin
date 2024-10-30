<?php

/**
 * Edit page for AdminComponents
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminComponentEdit extends AdminObjectEdit
{
	// {{{ protected function getObjectClass()

	protected function getObjectClass()
	{
		return 'AdminComponent';
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return __DIR__.'/edit.xml';
	}

	// }}}
	// {{{ protected function getObjectUiValueNames()

	protected function getObjectUiValueNames()
	{
		return array(
			'title',
			'shortname',
			'section',
			'visible',
			'enabled',
			'description',
		);
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->initSections();
		$this->initGroups();
	}

	// }}}
	// {{{ protected function initSections()

	protected function initSections()
	{
		$section_flydown = $this->ui->getWidget('section');
		$section_flydown_options = SwatDB::getOptionArray(
			$this->app->db,
			'AdminSection',
			'title',
			'id',
			'displayorder'
		);

		$section_flydown->addOptionsByArray($section_flydown_options);
	}

	// }}}
	// {{{ protected function initGroups()

	protected function initGroups()
	{
		$group_list = $this->ui->getWidget('groups');
		$group_list_options = SwatDB::getOptionArray(
			$this->app->db,
			'AdminGroup',
			'title',
			'id',
			'title'
		);

		$group_list->addOptionsByArray($group_list_options);
	}

	// }}}

	// process phase
	// {{{ protected function validate()

	protected function validate(): void
	{
		$shortname_widget = $this->ui->getWidget('shortname');
		$shortname = $shortname_widget->value;

		$should_validate_shortname = (!$this->isNew() || $shortname != '');
		if ($should_validate_shortname &&
			!$this->validateShortname($shortname)) {
			$message = new SwatMessage(
				Admin::_('Shortname already exists and must be unique.'),
				'error'
			);

			$shortname_widget->addMessage($message);
		}
	}

	// }}}
	// {{{ protected function postSaveObject()

	protected function postSaveObject()
	{
		$this->updateGroupBindings();
	}

	// }}}
	// {{{ protected function updateGroupBindings()

	protected function updateGroupBindings()
	{
		$group_list = $this->ui->getWidget('groups');

		SwatDB::updateBinding(
			$this->app->db,
			'AdminComponentAdminGroupBinding',
			'component',
			$this->getObject()->id,
			'groupnum',
			$group_list->values,
			'AdminGroup',
			'id'
		);
	}

	// }}}
	// {{{ protected function getSavedMessagePrimaryContent()

	protected function getSavedMessagePrimaryContent()
	{
		return sprintf(
			Admin::_('Component “%s” has been saved.'),
			$this->getObject()->title
		);
	}

	// }}}

	// build phase
	// {{{ protected function loadObject()

	protected function loadObject()
	{
		parent::loadObject();

		if (!$this->isNew()) {
			$this->loadGroupBindings();
		}
	}

	// }}}
	// {{{ protected function loadGroupBindings()

	protected function loadGroupBindings()
	{
		$group_list = $this->ui->getWidget('groups');
		$group_list->values = SwatDB::queryColumn(
			$this->app->db,
			'AdminComponentAdminGroupBinding',
			'groupnum',
			'component',
			$this->getObject()->id
		);
	}

	// }}}
}

?>
