<?php

/**
 * A link cell renderer to display in group headers.
 *
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminGroupLinkCellRenderer extends SwatLinkCellRenderer
{
    public function __construct()
    {
        parent::__construct();

        $this->addStyleSheet(
            'packages/admin/styles/admin-group-link-cell-renderer.css'
        );
    }

    /**
     * Gets the array of CSS classes that are applied to this user-interface
     * object.
     *
     * User-interface objects aggregate the list of user-specified classes and
     * may add static CSS classes of their own in this method.
     *
     * @return array the array of CSS classes that are applied to this
     *               user-interface object
     *
     * @see SwatUIObject::getCSSClassString()
     */
    protected function getCSSClassNames()
    {
        $classes = ['admin-group-link-cell-renderer'];

        return array_merge($classes, $this->classes);
    }
}
