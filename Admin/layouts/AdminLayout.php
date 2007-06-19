<?php

require_once 'Swat/SwatYUI.php';
require_once 'Site/layouts/SiteLayout.php';

/**
 * Base class for admin layouts
 *
 * @package   Admin
 * @copyright 2006-2007 silverorange
 */
abstract class AdminLayout extends SiteLayout
{
	// build phase
	// {{{ public function build()

	public function build()
	{
		parent::build();

		$yui = new SwatYUI(array('fonts', 'grids'));
		$this->addHtmlHeadEntrySet($yui->getHtmlHeadEntrySet());

		$this->addHtmlHeadEntry(new SwatStyleSheetHtmlHeadEntry(
			'packages/admin/styles/admin-layout.css', Admin::PACKAGE_ID));
	}

	// }}}
}

?>
