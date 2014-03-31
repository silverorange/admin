<?php

require_once 'Admin/pages/AdminObjectEdit.php';
require_once 'Admin/dataobjects/AdminSection.php';

/**
 * Edit page for AdminSections
 *
 * @package   Admin
 * @copyright 2005-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminSectionEdit extends AdminObjectEdit
{
	// {{{ protected function getObjectClass()

	protected function getObjectClass()
	{
		return 'AdminSection';
	}

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Admin/components/AdminSection/edit.xml';
	}

	// }}}

	// process phase
	// {{{ protected function updateObject()

	protected function updateObject()
	{
		parent::updateObject();

		$this->assignUiValues(
			array(
				'title',
				'visible',
				'description',
			)
		);
	}

	// }}}
	// {{{ protected function getSavedMessageText()

	protected function getSavedMessageText()
	{
		return sprintf(
			Admin::_('Section “%s” has been saved.'),
			$this->getObject()->title
		);
	}

	// }}}

	// build phase
	// {{{ protected function loadObject()

	protected function loadObject()
	{
		$this->assignValuesToUi(
			array(
				'title',
				'visible',
				'description',
			)
		);
	}

	// }}}
}

?>
