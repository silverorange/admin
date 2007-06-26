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
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminSubComponent extends SwatDBDataObject
{
	// {{{ public properties

	/**
	 * The component that contains this sub-component 
	 *
	 * not sure what @var
	 */
	public $component;

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
	 * <i>$enabled</i> property overrides the {@link AdminSubComponent::$show}
	 * property.
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
	public $show;

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'AdminSubComponent';
		$this->id_field = 'integer:id';
		$this->registerInternalProperty('section', 'AdminSection');
	}

	// }}}
}

?>
