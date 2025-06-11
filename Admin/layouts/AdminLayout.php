<?php

/**
 * Base class for admin layouts
 *
 * @package   Admin
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class AdminLayout extends SiteLayout
{
	// build phase


	public function build()
	{
		parent::build();

		$yui = new SwatYUI(array('fonts', 'grids'));
		$this->addHtmlHeadEntrySet($yui->getHtmlHeadEntrySet());

		$this->addHtmlHeadEntry('packages/admin/styles/admin-layout.css');
		$this->addHtmlHeadEntry('packages/admin/styles/admin-swat-local.css');
	}




	protected function getTagByFlagFile()
	{
		$tag = null;

		$www_root = dirname($_SERVER['SCRIPT_FILENAME']);
		$filename = $www_root.DIRECTORY_SEPARATOR.
			'..'.DIRECTORY_SEPARATOR.'.resource-tag';

		if (file_exists($filename) && is_readable($filename)) {
			$tag = trim(file_get_contents($filename));
		}

		return $tag;
	}


}

?>
