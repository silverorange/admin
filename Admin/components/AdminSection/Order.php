<?php

/**
 * Order page for AdminSections component.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminAdminSectionOrder extends AdminDBOrder
{
    private $parent;

    // init phase

    protected function initInternal()
    {
        parent::initInternal();

        $this->parent = SiteApplication::initVar('parent');
        $form = $this->ui->getWidget('order_form');
        $form->addHiddenField('parent', $this->parent);
    }

    // process phase

    protected function saveIndex($id, $index)
    {
        SwatDB::updateColumn(
            $this->app->db,
            'AdminSection',
            'integer:displayorder',
            $index,
            'integer:id',
            [$id]
        );
    }

    // build phase

    protected function buildInternal()
    {
        $frame = $this->ui->getWidget('order_frame');
        $frame->title = Admin::_('Order Sections');
        parent::buildInternal();
    }

    protected function loadData()
    {
        $order_widget = $this->ui->getWidget('order');
        $order_widget->addOptionsByArray(SwatDB::getOptionArray(
            $this->app->db,
            'AdminSection',
            'title',
            'id',
            'displayorder, title'
        ));

        $sql = 'select sum(displayorder) from AdminSection';
        $sum = SwatDB::queryOne($this->app->db, $sql, 'integer');
        $options_list = $this->ui->getWidget('options');
        $options_list->value = ($sum == 0) ? 'auto' : 'custom';
    }
}
