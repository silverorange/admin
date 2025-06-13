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

        $this->row_wrapper_class = SwatDBClassMap::get(AdminUser::class);
        $this->index_field = 'id';
    }
}
