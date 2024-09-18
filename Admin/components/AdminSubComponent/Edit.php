<?php

/**
 * Edit page for AdminSubComponents
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminSubComponentEdit extends AdminObjectEdit
{


	protected $admin_component;



	protected function getObjectClass()
	{
		return 'AdminSubComponent';
	}



	protected function getUiXml()
	{
		return __DIR__.'/edit.xml';
	}



	protected function getObjectUiValueNames()
	{
		return array(
			'title',
			'shortname',
			'visible',
		);
	}


	// init phase


	protected function initInternal()
	{
		parent::initInternal();

		$this->initAdminComponent();
	}



	protected function initAdminComponent()
	{
		if ($this->isNew()) {
			$parent_id = SiteApplication::initVar('parent');

			if ($parent_id === null) {
				throw new AdminNotFoundException(
					'Must supply a Component ID for newly created '.
					'Sub-Compoenets.'
				);
			}

			$class_name = SwatDBClassMap::get('AdminComponent');
			$this->admin_component = new $class_name();
			$this->admin_component->setDatabase($this->app->db);

			if (!$this->admin_component->load($parent_id)) {
				throw new AdminNotFoundException(
					sprintf(
						'Component with id "%s" not found.',
						$parent_id
					)
				);
			}
		} else {
			$this->admin_component = $this->getObject()->component;
		}
	}


	// process phase


	protected function validate()
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



	protected function updateObject()
	{
		parent::updateObject();

		if ($this->isNew()) {
			$this->getObject()->component = $this->admin_component;
		}
	}



	protected function getSavedMessagePrimaryContent()
	{
		return sprintf(
			Admin::_('Sub-Component “%s” has been saved.'),
			$this->getObject()->title
		);
	}


	// build phase


	protected function buildForm()
	{
		parent::buildForm();

		$form = $this->ui->getWidget('edit_form');
		$form->addHiddenField('parent', $this->admin_component->id);
	}



	protected function buildNavBar()
	{
		$this->navbar->popEntry();

		$this->navbar->createEntry(
			Admin::_('Admin Components'),
			'AdminComponent'
		);

		$this->navbar->createEntry(
			$this->admin_component->title,
			sprintf(
				'AdminComponent/Details?id=%s',
				$this->admin_component->id
			)
		);

		$this->navbar->createEntry(
			($this->isNew())
				? Admin::_('Add Sub-Component')
				: Admin::_('Edit Sub-Component')
		);
	}

}

?>
