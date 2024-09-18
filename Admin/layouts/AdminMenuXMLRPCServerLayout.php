<?php

/**
 * @package   Admin
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminMenuXMLRPCServerLayout extends SiteXMLRPCServerLayout
{


	/**
	 * This application's menu view
	 *
	 * @var AdminMenuView
	 */
	public $menu = null;


	// init phase


	public function init()
	{
		parent::init();
		$this->initMenu();
	}



	/**
	 * Initializes layout menu view
	 */
	protected function initMenu()
	{
		if ($this->menu === null) {
			$menu_store = SwatDB::executeStoredProc($this->app->db,
				'getAdminMenu',
				$this->app->db->quote($this->app->session->getUserId(),
					'integer'),
				'AdminMenuStore');

			$class = $this->app->getMenuViewClass();
			$this->menu = new $class($menu_store, $this->app);
		}

		$this->menu->init();
	}

}

?>
