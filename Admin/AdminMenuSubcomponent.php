<?php

/**
 * Admin menu sub component.
 *
 * Internal data class used internally within {@link AdminMenuStore}.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminMenuSubcomponent
{
    public $shortname;
    public $title;

    public function __construct($shortname, $title)
    {
        $this->shortname = $shortname;
        $this->title = $title;
    }
}
