<?php

require_once 'Admin/pages/AdminDBDelete.php';

/**
 * Admin delete page for SwatDBDataObjects
 *
 * @package   Admin
 * @copyright 2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminObjectDelete extends AdminDBDelete
{
	// {{{ protected properties

	/**
	 * The dataobjects instance we are deleting on the page.
	 *
	 * @var SwatDBRecordsetWrapper
	 *
	 * @see AdminObjectDelete::getObjects()
	 */
	protected $objects;

	// }}}
	// {{{ abstract protected function getRecordsetWrapperClass()

	abstract protected function getRecordsetWrapperClass();

	// }}}
	// {{{ protected function getUiXml()

	protected function getUiXml()
	{
		return 'Admin/pages/confirmation.xml';
	}

	// }}}
	// {{{ protected function getResolvedRecordsetWrapperClass()

	protected function getResolvedRecordsetWrapperClass()
	{
		return SwatDBClassMap::get($this->getRecordsetWrapperClass());
	}

	// }}}
	// {{{ protected function getObjects()

	protected function getObjects()
	{
		return $this->objects;
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		// AdminConfirmation still requires $ui_xml be set to load custom xml.
		$this->ui_xml = $this->getUiXml();

		parent::initInternal();

		$this->initObjects();
	}

	// }}}
	// {{{ protected function initObjects()

	protected function initObjects()
	{
		$wrapper_class = $this->getResolvedRecordsetWrapperClass();

		$this->objects = SwatDB::query(
			$this->app->db,
			$this->getObjectsSql(),
			$wrapper_class
		);

		if ($this->app->hasModule('SiteMemcacheModule')) {
			$this->objects->setFlushableCache(
				$this->app->getModule('SiteMemcacheModule')
			);
		}

		if (count($this->objects) === 0) {
			throw new AdminNotFoundException(
				sprintf(
					'No rows loaded for ‘%s’ wrapper',
					$wrapper_class
				)
			);
		}
	}

	// }}}
	// {{{ abstract protected function getObjectsSql()

	abstract protected function getObjectsSql();

	// }}}

	// process phase
	// {{{ protected function processDBData()

	protected function processDBData()
	{
		parent::processDBData();

		// Build the message before actually deleting the objects, so that the
		// message can have access to the objects for fancier messages.
		$message = $this->getDeletedMessage();

		$objects = $this->getObjects();
		$this->deleteObjects($objects);

		if ($message instanceof SiteMessage) {
			$this->app->messages->add($message);
		}
	}

	// }}}
	// {{{ protected function deleteObjects()

	protected function deleteObjects(SwatDBRecordsetWrapper $objects)
	{
		foreach ($objects as $object) {
			$this->deleteObject($object);
		}
	}

	// }}}
	// {{{ protected function deleteObject()

	protected function deleteObject(SwatDBDataObject $object)
	{
		$object->delete();
	}

	// }}}
	// {{{ protected function getDeletedMessage()

	protected function getDeletedMessage()
	{
		$message = null;

		$message_type      = $this->getDeletedMessageType();
		$primary_content   = $this->getDeletedMessagePrimaryContent();
		$secondary_content = $this->getDeletedMessageSecondaryContent();
		$content_type      = $this->getDeletedMessageContentType();

		if ($primary_content != '') {
			$message = new SwatMessage($primary_content, $message_type);

			if ($secondary_content != '') {
				$message->secondary_content = $secondary_content;
			}

			if ($content_type != '') {
				$message->content_type = $content_type;
			}
		}

		return $message;
	}

	// }}}
	// {{{ protected function getDeletedMessagePrimaryContent()

	protected function getDeletedMessagePrimaryContent()
	{
		return null;
	}

	// }}}
	// {{{ protected function getDeletedMessageSecondaryContent()

	protected function getDeletedMessageSecondaryContent()
	{
		return null;
	}

	// }}}
	// {{{ protected function getDeletedMessageType()

	protected function getDeletedMessageType()
	{
		return null;
	}

	// }}}
	// {{{ protected function getDeletedMessageContentType()

	protected function getDeletedMessageContentType()
	{
		return null;
	}

	// }}}
}

?>
