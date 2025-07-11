<?php

/**
 * A recordset wrapper class for AdminUserHistory objects.
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       AdminUserHistory
 */
class AdminUserHistoryWrapper extends SwatDBRecordsetWrapper
{
    protected function init()
    {
        parent::init();
        $this->row_wrapper_class = AdminUserHistory::class;
        $this->index_field = 'id';
    }
}
