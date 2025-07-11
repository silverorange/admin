<?php

/**
 * Admin menu section.
 *
 * Internal data class used internally within {@link AdminMenuStore}.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminMenuSection
{
    public $id;
    public $title;
    public $components;
    public $show;

    public function __construct($id, $title)
    {
        $this->id = $id;
        $this->title = $title;
        $this->components = [];
        $this->show = true;
    }
}
