<?php

/**
 * Generic admin database confirmation page.
 *
 * This class is intended to be a convenience base class. For a fully custom
 * DB confirmation page, inherit directly from AdminConfirmation instead.
 *
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminDBConfirmation extends AdminConfirmation
{
    /**
     * The current items of this confirmation page.
     *
     * @var SwatViewSelection
     *
     * @see AdminDBConfirmation::setItems()
     */
    protected $items;

    /**
     * Whether the extended "all items" checkbox was checked or not.
     *
     * @var bool
     *
     * @see SwatCheckAll::isExtendedSelected()
     */
    protected $extended_selected;

    /**
     * Creates a new database-driven confirmation page.
     *
     * @param SiteLayout $layout optional
     */
    public function __construct(
        SiteApplication $app,
        ?SiteLayout $layout = null,
        array $arguments = []
    ) {
        parent::__construct($app, $layout, $arguments);

        // don't use setItems() here because the UI has not been constructed
        // yet and the hidden value cannot be added to the form
        $this->items = new SwatViewSelection([]);
    }

    /**
     * Sets the items of this confirmation page.
     *
     * @param array|SwatViewSelection $items             the items of this confirmation
     *                                                   page. Developers are encouraged
     *                                                   to use a SwatViewSelection; array
     *                                                   is provided for backwards
     *                                                   compatibility.
     * @param bool                    $extended_selected whether the extended "all items"
     *                                                   checkbox was checked or not
     */
    public function setItems($items, $extended_selected = false)
    {
        $this->extended_selected = $extended_selected;

        if (is_array($items)) {
            $items = new SwatViewSelection($items);
        }

        if (!$items instanceof SwatViewSelection) {
            throw new SwatInvalidClassException(
                'The $items parameter must be either a SwatViewSelection or ' .
                'an array.',
                0,
                $items
            );
        }

        $this->items = $items;

        // Add a hidden field to this confirmation page's form to store the
        // current items of this confirmation page across requests
        $form = $this->ui->getWidget('confirmation_form');
        $form->addHiddenField('items', $this->items);
        $form->addHiddenField('extended_selected', $this->extended_selected);
    }

    /**
     * Get the items of this confirmation page as a database-quoted list.
     *
     * @param string $type optional. The MDB2 datatype used to quote the items.
     *                     By default, 'integer' is used.
     *
     * @return string a comma-seperated, database-quoted list of items
     */
    protected function getItemList($type = 'integer')
    {
        $list = [];

        foreach ($this->items as $item) {
            $list[] = $this->app->db->quote($item, $type);
        }

        return implode(',', $list);
    }

    /**
     * Gets the number of items on this confirmation page.
     *
     * @return int the number of items on this confirmation page
     */
    protected function getItemCount()
    {
        return count($this->items);
    }

    /**
     * Gets the first item on this confirmation page.
     *
     * @return mixed the first item
     *
     * @see AdminDBConfirmation::setItems()
     */
    protected function getFirstItem()
    {
        $this->items->rewind();

        return $this->items->current();
    }

    // init phase

    protected function initInternal()
    {
        parent::initInternal();

        $form = $this->ui->getWidget('confirmation_form');
        $items = $form->getHiddenField('items');
        if ($items !== null) {
            $this->setItems($items);
        }
    }

    // process phase

    protected function processResponse(): void
    {
        $form = $this->ui->getWidget('confirmation_form');

        if ($this->ui->getWidget('yes_button')->hasBeenClicked()) {
            try {
                $transaction = new SwatDBTransaction($this->app->db);
                $this->processDBData();
                $transaction->commit();
            } catch (SwatDBException $e) {
                $transaction->rollback();
                $this->generateMessage($e);
                $e->processAndContinue();
            } catch (SwatException $e) {
                $this->generateMessage($e);
                $e->processAndContinue();
            }
        }
    }

    protected function generateMessage(Throwable $e)
    {
        if ($e instanceof SwatDBException) {
            $message = new SwatMessage(
                Admin::_('A database error has occured.'),
                SwatMessage::SYSTEM_ERROR
            );
        } else {
            $message = new SwatMessage(
                Admin::_('An error has occured.'),
                SwatMessage::SYSTEM_ERROR
            );
        }

        $this->app->messages->add($message);
    }

    /**
     * Processes data in the database.
     *
     * This method is called to process data after an affirmative
     * confirmation response. Sub-classes should implement this method and
     * perform whatever actions are necessary to process the response.
     */
    protected function processDBData(): void
    {
        $form = $this->ui->getWidget('confirmation_form');
        $this->setItems(
            $form->getHiddenField('items'),
            $form->getHiddenField('extended_selected')
        );
    }

    // build phase

    protected function buildInternal()
    {
        parent::buildInternal();

        $id = SiteApplication::initVar('id', null, SiteApplication::VAR_GET);
        if ($id !== null) {
            $this->setItems(new SwatViewSelection([$id]));
        }
    }
}
