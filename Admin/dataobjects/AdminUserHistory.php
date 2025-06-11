<?php

/**
 * History record for an admin user.
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminUserHistory extends SwatDBDataObject
{
    /**
     * Unique identifier.
     *
     * @var int
     */
    public $id;

    /**
     * Date an admin user logged in to the admin.
     *
     * @var SwatDate
     */
    public $login_date;

    /**
     * HTTP user-agent used by an admin user to log in to the admin.
     *
     * @var string
     */
    public $login_agent;

    /**
     * IP address used by an admin user to log in to the admin.
     *
     * @var string
     */
    public $remote_ip;

    protected function init()
    {
        $this->table = 'AdminUserHistory';
        $this->id_field = 'integer:id';
        $this->registerDateProperty('login_date');
        $this->registerInternalProperty('instance', 'Instance');
    }
}
