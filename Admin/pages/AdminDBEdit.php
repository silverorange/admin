<?php

/**
 * Generic admin database edit page.
 *
 * This class is intended to be a convenience base class. For a fully custom
 * edit page, inherit directly from AdminPage instead.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminDBEdit extends AdminEdit
{
    // process phase

    protected function saveData(): bool
    {
        $relocate = true;
        $message = null;

        try {
            $transaction = new SwatDBTransaction($this->app->db);
            $this->saveDBData();
            $transaction->commit();
        } catch (SwatDBException $e) {
            $transaction->rollback();

            $message = new SwatMessage(
                Admin::_(
                    'A database error has occurred. The item was not saved.'
                ),
                'system-error'
            );

            $e->processAndContinue();
            $relocate = false;
        } catch (SwatException $e) {
            $message = new SwatMessage(
                Admin::_(
                    'An error has occurred. The item was not saved.'
                ),
                'system-error'
            );

            $e->processAndContinue();
            $relocate = false;
        }

        if ($message !== null) {
            $this->app->messages->add($message);
        }

        return $relocate;
    }

    /**
     * Save the data from the database.
     *
     * This method is called to save data from the widgets after processing.
     * Sub-classes should implement this method and perform whatever actions
     * are necessary to store the data. Widgets can be accessed through the
     * $ui class variable.
     */
    abstract protected function saveDBData(): void;

    // build phase

    protected function loadData()
    {
        $this->loadDBData();

        return true;
    }

    /**
     * Load the data from the database.
     *
     * This method is called to load data to be edited into the widgets.
     * Sub-classes should implement this method and perform whatever actions
     * are necessary to obtain the data. Widgets can be accessed through the
     * $ui class variable.
     */
    abstract protected function loadDBData();
}
