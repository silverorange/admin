<?php

/**
 * Component to perform a particular administration task.
 *
 * Components are the main organizational unit in the Admin package. Each
 * component is composed of a set of AdminPage objects that work together to
 * administer an item.
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminComponent extends SwatDBDataObject
{
    /**
     * Unique identifier.
     *
     * @var int
     */
    public $id;

    /**
     * Shortname of this component.
     *
     * This shortname is used for building Admin page URIs.
     *
     * @var string
     */
    public $shortname;

    /**
     * Title of this component.
     *
     * @var string
     */
    public $title;

    /**
     * Optional description of this component.
     *
     * @var string
     */
    public $description;

    /**
     * Order of display of this component relative to other components in this
     * component's section.
     *
     * @var string
     */
    public $displayorder;

    /**
     * Whether or not this component is enabled.
     *
     * If a component is not enabled, it is inaccessible to all users. The
     * <i>$enabled</i> property overrides the {@link AdminComponent::$visible}
     * property.
     *
     * @var bool
     */
    public $enabled;

    /**
     * Whether or not links to this component should be shown in the admin.
     *
     * This property does not affect the ability of users to load this
     * component. It only affects whether or not links to this component are
     * displayed.
     *
     * @var bool
     */
    public $visible;

    /**
     * Loads an admin component by its shortname.
     *
     * @param string $shortname the shortname of the admin component to load
     *
     * @return bool true if loading this component was successful and
     *              false if a component with the given shortname does not
     *              exist
     */
    public function loadByShortname($shortname)
    {
        $this->checkDB();

        $row = null;

        if ($this->table !== null) {
            $sql = sprintf(
                'select * from %s where shortname = %s',
                $this->table,
                $this->db->quote($shortname, 'text')
            );

            $rs = SwatDB::query($this->db, $sql, null);
            $row = $rs->fetchRow(MDB2_FETCHMODE_ASSOC);
        }

        if ($row === null) {
            return false;
        }

        $this->initFromRow($row);
        $this->generatePropertyHashes();

        return true;
    }

    /**
     * Loads an admin component by its shortname.
     *
     * @param string $shortname the shortname of the admin component to load
     *
     * @return bool true if loading this component was successful and
     *              false if a component with the given shortname does not
     *              exist
     *
     * @deprecated Use {@link AdminComponent::loadByShortname}
     */
    public function loadFromShortname($shortname)
    {
        return $this->loadByShortname($shortname);
    }

    protected function init()
    {
        $this->table = 'AdminComponent';
        $this->id_field = 'integer:id';

        $this->registerInternalProperty(
            'section',
            SwatDBClassMap::get(AdminSection::class)
        );
    }

    /**
     * @return AdminSubComponentWrapper
     */
    protected function loadSubComponents()
    {
        $sql = sprintf(
            'select * from AdminSubComponent where component = %s',
            $this->db->quote($this->id, 'integer')
        );

        return SwatDB::query(
            $this->db,
            $sql,
            SwatDBClassMap::get(AdminSubComponentWrapper::class)
        );
    }

    /**
     * @return AdminGroupWrapper
     */
    protected function loadGroups()
    {
        $sql = sprintf(
            'select * from AdminGroup
			inner join AdminComponentAdminGroupBinding as binding on
				binding.groupnum = AdminGroup.id and binding.component = %s',
            $this->db->quote($this->id, 'integer')
        );

        return SwatDB::query(
            $this->db,
            $sql,
            SwatDBClassMap::get(AdminGroupWrapper::class)
        );
    }
}
