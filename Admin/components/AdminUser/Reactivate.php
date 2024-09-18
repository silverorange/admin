<?php

/**
 * Reactivate confirmation page for AdminUsers component.
 *
 * @copyright 2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminUserReactivate extends AdminDBConfirmation
{
    // init phase

    protected function initInternal()
    {
        parent::initInternal();
        $this->navbar->popEntry();
        $this->navbar->createEntry(Admin::_('Reactivate'));
    }

    // process phase

    protected function processDBData()
    {
        parent::processDBData();

        $locale = SwatI18NLocale::get();

        $now = new SwatDate();
        $now->toUTC();

        $sql = sprintf(
            'update AdminUser set activation_date = %s
			where id in (%s)',
            $this->app->db->quote($now->getDate(), 'date'),
            $this->getItemList('integer')
        );

        $num = SwatDB::exec($this->app->db, $sql);

        $this->app->messages->add(
            new SwatMessage(
                sprintf(
                    Admin::ngettext(
                        'One admin user has been reactivated.',
                        '%s admin users have been reactivated.',
                        $num
                    ),
                    $locale->formatNumber($num)
                )
            )
        );
    }

    // build phase

    public function buildInternal()
    {
        parent::buildInternal();

        $users = SwatDB::query(
            $this->app->db,
            sprintf(
                'select name from AdminUser
				where id in (%s)
				order by name asc',
                $this->getItemList('integer')
            ),
            AdminUserWrapper::class
        );

        ob_start();

        $h3_tag = new SwatHtmlTag('h3');
        $h3_tag->setContent(
            Admin::ngettext(
                'Reactivate the following admin user?',
                'Reactivate the following admin users?',
                count($users)
            )
        );
        $h3_tag->display();

        if (count($users) > 0) {
            echo '<ul>';
            foreach ($users as $user) {
                $li_tag = new SwatHtmlTag('li');
                $li_tag->setContent($user->name);
                $li_tag->display();
            }
            echo '</ul>';
        }

        $message = $this->ui->getWidget('confirmation_message');
        $message->content = ob_get_clean();
        $message->content_type = 'text/xml';

        if (count($users) === 0) {
            $this->switchToCancelButton();
        }
    }
}
