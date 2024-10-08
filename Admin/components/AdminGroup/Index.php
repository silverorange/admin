<?php

/**
 * Index page for AdminGroups component.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminGroupIndex extends AdminIndex
{
    // init phase

    protected function initInternal()
    {
        $this->ui->loadFromXML(__DIR__ . '/index.xml');
    }

    // process phase

    protected function processActions(SwatView $view, SwatActions $actions)
    {
        switch ($actions->selected->id) {
            case 'delete':
                $this->app->replacePage('AdminGroup/Delete');
                $this->app->getPage()->setItems($view->getSelection());
                break;
        }
    }

    // build phase

    protected function getTableModel(SwatView $view)
    {
        $sql = 'select id, title from AdminGroup order by title';

        return SwatDB::query($this->app->db, $sql, AdminGroupWrapper::class);
    }
}
