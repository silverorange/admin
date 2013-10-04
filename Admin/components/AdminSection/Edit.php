<?php

require_once 'Admin/pages/AdminDBEdit.php';
require_once 'Admin/AdminUI.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Admin/dataobjects/AdminSection.php';
require_once 'MDB2.php';


/**
 * Edit page for AdminSections
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminSectionEdit extends AdminDBEdit
{
	// {{{ private properties

	private $section;

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();
		$this->initSection();

		$this->ui->loadFromXML(__DIR__.'/edit.xml');
	}

	// }}}
	// {{{ protected function initSection()
	protected function initSection()
	{
		$class_name = SwatDBClassMap::get('AdminSection');
		$this->section = new $class_name();
		$this->section->setDatabase($this->app->db);

		if ($this->id !== null) {
			if (!$this->section->load($this->id)){
				throw new AdminNotFoundException(
					sprintf(Admin::_('Section with id "%s" not found.'),
							$this->id));
			}
		}
	}

	// }}}

	// process phase
	// {{{ protected function saveDBData()

	protected function saveDBData()
	{
		$values = $this->ui->getValues(array(
			'title',
			'visible',
			'description'));

		$this->section->title       = $values['title'];
		$this->section->visible     = $values['visible'];
		$this->section->description = $values['description'];
		$this->section->save();

		$message = new SwatMessage(
			sprintf(Admin::_('Section “%s” has been saved.'),
			$values['title']), 'notice');

		$this->app->messages->add($message);
	}

	// }}}

	// build phase
	// {{{ protected function loadDBData()

	protected function loadDBData()
	{
		$this->ui->setValues(get_object_vars($this->section));
	}

	// }}}
}

?>
