<?php

/**
 * Section to group multiple components together.
 *
 * Sections group components together for organization and display. Similar
 * components should be grouped in the same section. Sections are not related
 * to component access.
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminSection extends SwatDBDataObject
{
    /**
     * Unique identifier.
     *
     * @var int
     */
    public $id;

    /**
     * Title of this section.
     *
     * @var string
     */
    public $title;

    /**
     * Optional description of this section.
     *
     * @var string
     */
    public $description;

    /**
     * Order of display of this section relative ot other sections.
     *
     * @var int
     */
    public $displayorder;

    /**
     * Whether or not this section is shown when sections are displayed.
     *
     * @var bool
     */
    public $visible;

    protected function init()
    {
        $this->table = 'AdminSection';
        $this->id_field = 'integer:id';
    }

    /**
     * @return AdminComponentWrapper
     */
    protected function loadComponents()
    {
        $sql = sprintf(
            'select * from AdminComponent
			where section = %s
			order by displayorder, title',
            $this->db->quote($this->id, 'integer')
        );

        return SwatDB::query($this->db, $sql, AdminComponentWrapper::class);
    }
}
