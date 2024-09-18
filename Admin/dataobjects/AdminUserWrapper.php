<?php

/**
 * A recordset wrapper class for AdminUser objects.
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       AdminUser
 */
class AdminUserWrapper extends SwatDBRecordsetWrapper
{
    protected function init()
    {
        parent::init();

        $admin_user_class =
            SwatDBClassMap::get('AdminUser');

        $this->row_wrapper_class = $admin_user_class;
        $this->index_field = 'id';
    }
}
