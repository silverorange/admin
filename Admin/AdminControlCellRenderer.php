<?php

/**
 * Details Control.
 *
 * Convenience class for a Details button
 *
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @deprecated use {@link AdminTitleLinkCellRenderer} or one of its subclasses
 *              instead
 */
class AdminControlCellRenderer extends SwatImageLinkCellRenderer
{
    /**
     * The stock id of this AdminControlCellRenderer.
     *
     * Specifying a stock id initializes this control cell renderer with
     * a set of stock values.
     *
     * @var string
     *
     * @see AdminControlCellRenderer::setFromStock()
     */
    public $stock_id;

    public function render()
    {
        if (!$this->visible) {
            return;
        }

        if ($this->stock_id === null) {
            $this->setFromStock('details', false);
        } else {
            $this->setFromStock($this->stock_id, false);
        }

        parent::render();
    }

    /**
     * Sets the values of this control cell renderer to a stock type.
     *
     * Valid stock type ids are:
     *
     * - details (default)
     * - edit
     *
     * @param string $stock_id             the identifier of the stock type to use
     * @param bool   $overwrite_properties whether to overwrite properties if
     *                                     they are already set
     *
     * @throws SwatUndefinedStockTypeException
     */
    public function setFromStock($stock_id, $overwrite_properties = true)
    {
        switch ($stock_id) {
            case 'details':
                $title = Admin::_('View Details');
                $alt = Admin::_('Details');
                $image = 'packages/admin/images/admin-generic-document.png';
                $width = '22';
                $height = '22';
                break;

            case 'edit':
                $title = Admin::_('Edit');
                $alt = Admin::_('Edit');
                $image = 'packages/admin/images/admin-edit.png';
                $width = '22';
                $height = '22';
                break;

            default:
                throw new SwatUndefinedStockTypeException(
                    "Stock type with id of '{$stock_id}' not found.",
                    0,
                    $stock_id
                );
        }

        if ($overwrite_properties || ($this->title === null)) {
            $this->title = $title;
        }

        if ($overwrite_properties || ($this->alt === null)) {
            $this->alt = $alt;
        }

        if ($overwrite_properties || ($this->image === null)) {
            $this->image = $image;
        }

        if ($overwrite_properties || ($this->width === null)) {
            $this->width = $width;
        }

        if ($overwrite_properties || ($this->height === null)) {
            $this->height = $height;
        }
    }
}
