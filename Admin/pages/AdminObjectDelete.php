<?php

/**
 * Admin delete page for SwatDBDataObjects.
 *
 * @copyright 2014-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminObjectDelete extends AdminDBDelete
{
    /**
     * The dataobjects instance we are deleting on the page.
     *
     * @var SwatDBRecordsetWrapper
     *
     * @see AdminObjectDelete::getObjects()
     */
    protected $objects;

    abstract protected function getRecordsetWrapperClass();

    protected function getUiXml()
    {
        return __DIR__ . '/confirmation.xml';
    }

    protected function getResolvedRecordsetWrapperClass()
    {
        return SwatDBClassMap::get($this->getRecordsetWrapperClass());
    }

    protected function getObjects()
    {
        return $this->objects;
    }

    // init phase

    protected function initInternal()
    {
        parent::initInternal();

        $this->initObjects();
    }

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

    abstract protected function getObjectsSql();

    // process phase

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

    protected function deleteObjects(SwatDBRecordsetWrapper $objects)
    {
        foreach ($objects as $object) {
            $this->deleteObject($object);
        }
    }

    protected function deleteObject(SwatDBDataObject $object)
    {
        $object->delete();
    }

    protected function getDeletedMessage()
    {
        $message = null;

        $message_type = $this->getDeletedMessageType();
        $primary_content = $this->getDeletedMessagePrimaryContent();
        $secondary_content = $this->getDeletedMessageSecondaryContent();
        $content_type = $this->getDeletedMessageContentType();

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

    protected function getDeletedMessagePrimaryContent()
    {
        return null;
    }

    protected function getDeletedMessageSecondaryContent()
    {
        return null;
    }

    protected function getDeletedMessageType()
    {
        return null;
    }

    protected function getDeletedMessageContentType()
    {
        return null;
    }

    // build phase

    protected function buildInternal()
    {
        parent::buildInternal();

        $this->buildConfirmationMessage();
    }

    /**
     * Allows building a default confirmation message including a header and
     * further body.
     *
     * This is optional and by default does nothing, but does allow for
     * some standard admin markup for delete confirmation messages that are not
     * AdminDependencies by subclassing only some of it's parts.
     */
    protected function buildConfirmationMessage()
    {
        $message_content = $this->getConfirmationMessageContent();
        if ($message_content != '') {
            $message = $this->ui->getWidget('confirmation_message');
            $message->content = $message_content;

            $content_type = $this->getConfirmationMessageContentType();
            if ($content_type != '') {
                $message->content_type = $content_type;
            }
        }
    }

    protected function getConfirmationMessageContent()
    {
        $confirmation_message = '';

        $header_message = $this->getConfirmationMessageHeader();
        if ($header_message != '') {
            $header = new SwatHtmlTag('h3');
            $header->setContent($header_message);
            $confirmation_message .= $header;
        }

        $body_message = $this->getConfirmationMessageBody();
        if ($body_message != '') {
            $confirmation_message .= $body_message;
        }

        return $confirmation_message;
    }

    protected function getConfirmationMessageContentType()
    {
        return 'text/xml';
    }

    protected function getConfirmationMessageHeader()
    {
        return '';
    }

    protected function getConfirmationMessageBody()
    {
        return '';
    }
}
