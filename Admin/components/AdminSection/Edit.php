<?php

/**
 * Edit page for AdminSections
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminSectionEdit extends AdminObjectEdit
{


	protected function getObjectClass()
	{
		return 'AdminSection';
	}




	protected function getUiXml()
	{
		return __DIR__.'/edit.xml';
	}




	protected function getObjectUiValueNames()
	{
		return array(
			'title',
			'visible',
			'description',
		);
	}



	// process phase


	protected function getSavedMessagePrimaryContent()
	{
		return sprintf(
			Admin::_('Section “%s” has been saved.'),
			$this->getObject()->title
		);
	}


}

?>
