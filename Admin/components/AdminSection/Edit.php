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
	// {{{ protected function getObjectUiValueNames()

	protected function getObjectUiValueNames()
	{
		return array(
			'title',
			'visible',
			'description',
		);
	}

	// }}}

	// process phase
	// {{{ protected function getSavedMessagePrimaryContent()

	protected function getSavedMessagePrimaryContent()
	{
		return sprintf(
			Admin::_('Section “%s” has been saved.'),
			$this->getObject()->title
		);
	}

	// }}}
}

?>
