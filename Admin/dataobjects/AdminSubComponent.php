<?php

require_once 'SwatDB/SwatDBDataObject.php';
require_once 'Admin/dataobjects/AdminSection.php';

/**
 * Sub-Component to perform a particular administration task within a component
 *
 * A part of a component designed to help achieve the complition of an
 * administative task.
 *
 * @package   Admin
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminSubComponent extends SwatDBDataObject
{
	// {{{ public properties

	/**
	 * Unique identifier
	 *
	 * @var integer
	 */
	public $id;

	/**
	 * Shortname of this sub-component
	 *
	 * This shortname is used for building Admin page URIs.
	 *
	 * @var string
	 */
	public $shortname;

	/**
	 * Title of this sub-component
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Optional description of this sub-component
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Order of display of this sub-component relative to other sub-components
	 * in this sub-component's section
	 *
	 * @var string
	 */
	public $displayorder;

	/**
	 * Whether or not this sub-component is enabled
	 *
	 * If a sub-component is not enabled, it is inaccessible to all users. The
	 * <i>$enabled</i> property overrides the
	 * {@link AdminSubComponent::$visible} property.
	 *
	 * @var boolean
	 */
	public $enabled;

	/**
	 * Whether or not links to this sub-component should be shown in the admin
	 *
	 * This property does not affect the ability of users to load this
	 * sub-component. It only affects whether or not links to this
	 * sub-component are displayed.
	 *
	 * @var boolean
	 */
	public $visible;

	// }}}
	// {{{ public function loadByShortname()

	/**
	 * Loads an admin sub-component by its shortname
	 *
	 * @param string $shortname the shortname of the sub-component to load.
	 *
	 * @return boolean true if loading this sub-component was successful and
	 *                  false if a sub-component with the given shortname does
	 *                  not exist.
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

	// }}}
	// {{{ public function loadFromShortname()

	/**
	 * Loads an admin sub-component by its shortname
	 *
	 * @param string $shortname the shortname of the sub-component to load.
	 *
	 * @return boolean true if loading this sub-component was successful and
	 *                  false if a sub-component with the given shortname does
	 *                  not exist.
	 *
	 * @deprecated Use {@link AdminSubComponent::loadByShortname}
	 */
	public function loadFromShortname($shortname)
	{
		return $this->loadByShortname($shortname);
	}

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'AdminSubComponent';
		$this->id_field = 'integer:id';

		$this->registerInternalProperty(
			'section',
			SwatDBClassMap::get('AdminSection')
		);

		$this->registerInternalProperty(
			'component',
			SwatDBClassMap::get('AdminComponent')
		);
	}

	// }}}
}

?>
