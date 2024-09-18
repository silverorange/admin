<?php

/**
 * A recordset wrapper class for AdminComponent objects.
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see       AdminComponent
 */
class AdminComponentWrapper extends SwatDBRecordsetWrapper
{
    protected function init()
    {
        parent::init();
        $this->row_wrapper_class = 'AdminComponent';
        $this->index_field = 'id';
    }
}
