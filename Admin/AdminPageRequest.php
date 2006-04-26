<?php

require_once 'Site/SiteObject.php';

/**
 * Page request
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminPageRequest extends SiteObject
{
	public $source;
	public $component;
	public $subcomponent;
	public $title;

	public function getFilename()
	{
		$classfile = $this->component.'/'.$this->subcomponent.'.php';
		$file = null;

		if (file_exists('../../include/admin/components/'.$classfile)) {
			$file = '../../include/admin/components/'.$classfile;
		} else {
			$paths = explode(':', ini_get('include_path'));

			foreach ($paths as $path) {
				if (file_exists($path.'/Admin/components/'.$classfile)) {
					$file = 'Admin/components/'.$classfile;
					break;
				}
			}
		}
		
		return $file;
	}

	public function getClassname()
	{
		$classname = $this->component.$this->subcomponent;
		
		if (class_exists($classname))
			return $classname;
		else
			return null;
	}
}

?>
