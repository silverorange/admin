<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'SwatDB/SwatDB.php';

/**
 * Edit page for AdminComponents
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminComponentsEdit extends AdminDBEdit
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
		$section_flydown->addOptionsByArray(SwatDB::getOptionArray($this->app->db, 
			'adminsections', 'title', 'id', 'displayorder'));

		$group_list = $this->ui->getWidget('groups');
		$group_list->options = SwatDB::getOptionArray($this->app->db, 
			'admingroups', 'title', 'id', 'title');

		$this->fields = array('title', 'shortname', 'integer:section', 
			'boolean:show', 'boolean:enabled', 'description');
	}

	// }}}

	// process phase
	// {{{ protected function validate()

	protected function validate($id)
	{
		$shortname = $this->ui->getWidget('shortname');

		$query = SwatDB::query($this->app->db, sprintf('select shortname from
			admincomponents where shortname = %s and id %s %s',
			$this->app->db->quote($shortname->value, 'text'),
			SwatDB::equalityOperator($id, true),
			$this->app->db->quote($id, 'integer')));

		if ($query->getCount() > 0) {
			$msg = new SwatMessage(Admin::_('Shortname already exists and must be unique.'), SwatMessage::ERROR);
			$shortname->addMessage($msg);
		}
	}

	// }}}
	// {{{ protected function saveDBData()

	protected function saveDBData($id)
	{
		$values = $this->ui->getValues(array('title', 'shortname', 'section', 
			'show', 'enabled', 'description'));

		if ($id == 0)
			$id = SwatDB::insertRow($this->app->db, 'admincomponents', $this->fields,
				$values, 'integer:id');
		else
			SwatDB::updateRow($this->app->db, 'admincomponents', $this->fields,
				$values, 'integer:id', $id);

		$group_list = $this->ui->getWidget('groups');

		SwatDB::updateBinding($this->app->db, 'admincomponent_admingroup', 
			'component', $id, 'groupnum', $group_list->values, 'admingroups', 'id');

		$msg = new SwatMessage(
			sprintf(Admin::_('Component &#8220;%s&#8221; has been saved.'),
			$values['title']), SwatMessage::NOTIFICATION);

		$this->app->messages->add($msg);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData($id)
	{
		$row = SwatDB::queryRowFromTable($this->app->db, 'admincomponents', 
			$this->fields, 'integer:id', $id);

		if ($row === null)
			return $this->app->replacePageNoAccess();

		$this->ui->setValues(get_object_vars($row));

		$group_list = $this->ui->getWidget('groups');
		$group_list->values = SwatDB::queryColumn($this->app->db, 
			'admincomponent_admingroup', 'groupnum', 'component', $id);
	}

	// }}}
}

?>
