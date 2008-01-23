<?php

require_once 'Swat/SwatLinkCellRenderer.php';

/**
 * A link cell renderer to display in group headers
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminGroupLinkCellRenderer extends SwatLinkCellRenderer
{
	// {{{ public function __construct()

	public function __construct()
	{
		parent::__construct();

		$this->addStyleSheet(
			'packages/admin/styles/admin-group-link-cell-renderer.css',
			Admin::PACKAGE_ID);
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this user-interface
	 * object
	 *
	 * User-interface objects aggregate the list of user-specified classes and
	 * may add static CSS classes of their own in this method.
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                user-interface object.
	 *
	 * @see SwatUIObject::getCSSClassString()
	 */
	protected function getCSSClassNames()
	{
		$classes = array('admin-group-link-cell-renderer');

		return array_merge($classes, $this->classes);
	}

	// }}}
}

?>
