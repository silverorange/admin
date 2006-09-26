<?php

require_once 'Site/layouts/SiteXMLRPCServerLayout.php';

/**
 * @package   Admin
 * @copyright 2006 silverorange
 */
class AdminMenuXMLRPCServerLayout extends SiteXMLRPCServerLayout
{
	// {{{ public properties

	/**
	 * This application's menu view
	 *
	 * @var AdminMenuView
	 */
	public $menu = null;

	// }}}

	// init phase
	// {{{ public function init()

	public function init()
	{
		parent::init();
		$this->initMenu();
	}

	// }}}
	// {{{ protected function initMenu()

	/**
	 * Initializes layout menu view
	 */
	protected function initMenu()
	{
		if ($this->menu === null) {
			$menu_store = SwatDB::executeStoredProc($this->app->db,
				'getAdminMenu', $this->app->db->quote($_SESSION['user_id'],
				'integer'), 'AdminMenuStore');

			$class = $this->app->getMenuViewClass();
			$this->menu = new $class($menu_store);
		}

		$this->menu->init();
	}

	// }}}
}

?>
