<?php

/**
 * Delete confirmation page for AdminComponents.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminComponentDelete extends AdminDBDelete
{
    // process phase

    protected function processDBData()
    {
        parent::processDBData();

        $sql = 'delete from AdminComponent where id in (%s)';
        $item_list = $this->getItemList('integer');
        $sql = sprintf($sql, $item_list);
        $num = SwatDB::exec($this->app->db, $sql);

        $message = new SwatMessage(sprintf(
            Admin::ngettext(
                'One component has been deleted.',
                '%s components have been deleted.',
                $num
            ),
            SwatString::numberFormat($num)
        ));

        $this->app->messages->add($message);
    }

    // build phase

    protected function buildInternal()
    {
        parent::buildInternal();

        $item_list = $this->getItemList('integer');

        $dep = new AdminListDependency();
        $dep->setTitle(Admin::_('component'), Admin::_('components'));
        $dep->entries = AdminListDependency::queryEntries(
            $this->app->db,
            'AdminComponent',
            'integer:id',
            null,
            'text:title',
            'displayorder, title',
            'id in (' . $item_list . ')',
            AdminDependency::DELETE
        );

        $dep_subcomponents = new AdminSummaryDependency();
        $dep_subcomponents->setTitle(
            Admin::_('sub-component'),
            Admin::_('sub-components')
        );

        $dep_subcomponents->summaries = AdminSummaryDependency::querySummaries(
            $this->app->db,
            'AdminSubComponent',
            'integer:id',
            'integer:component',
            'component in (' . $item_list . ')',
            AdminDependency::DELETE
        );

        $dep->addDependency($dep_subcomponents);

        $message = $this->ui->getWidget('confirmation_message');
        $message->content = $dep->getMessage();
        $message->content_type = 'text/xml';

        if ($dep->getStatusLevelCount(AdminDependency::DELETE) == 0) {
            $this->switchToCancelButton();
        }
    }
}
