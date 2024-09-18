<?php

/**
 * Delete confirmation page for AdminUsers component.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminUserDelete extends AdminDBDelete
{
    // process phase

    protected function processDBData()
    {
        parent::processDBData();

        $item_list = $this->getItemList('integer');
        $sql = sprintf(
            'delete from AdminUser where id in (%s) and id != %s',
            $item_list,
            $this->app->db->quote($this->app->session->getUserId(), 'integer')
        );

        $num = SwatDB::exec($this->app->db, $sql);

        $message = new SwatMessage(sprintf(
            Admin::ngettext(
                'One admin user has been deleted.',
                '%s admin users have been deleted.',
                $num
            ),
            SwatString::numberFormat($num)
        ));

        $this->app->messages->add($message);
    }

    // build phase

    public function buildInternal()
    {
        parent::buildInternal();

        $item_list = $this->getItemList('integer');

        $where_clause = sprintf(
            'id in (%s) and id != %s',
            $item_list,
            $this->app->db->quote($this->app->session->getUserId(), 'integer')
        );

        $dep = new AdminListDependency();
        $dep->setTitle(Admin::_('admin user'), Admin::_('admin users'));
        $dep->entries = AdminListDependency::queryEntries(
            $this->app->db,
            'AdminUser',
            'integer:id',
            null,
            'text:name',
            'name',
            $where_clause,
            AdminDependency::DELETE
        );

        $message = $this->ui->getWidget('confirmation_message');
        $message->content = $dep->getMessage();
        $message->content_type = 'text/xml';

        if ($dep->getItemCount() == 0) {
            $this->switchToCancelButton();
        }

        // display can't delete self message if current account is in selection
        if ($this->items->contains($this->app->session->getUserId())) {
            $header = new SwatHtmlTag('h3');
            $header->setContent(
                Admin::_('You cannot delete your own account.')
            );

            $message->content .= $header;
        }
    }
}
