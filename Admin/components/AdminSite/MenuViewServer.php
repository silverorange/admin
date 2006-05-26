<?php

require_once 'Admin/AdminXMLRPCServer.php';
require_once 'Admin/AdminMenuViewStateStore.php';

/**
 * Updates menu-view state stored in the user's session
 *
 * @package   Admin
 * @copyright 2006 silverorange
 */
class AdminSiteMenuViewServer extends AdminXMLRPCServer
{
	/**
	 * Sets the shown state of the entire menu-view
	 *
	 * @param boolean $state the shown state of the entire menu.
	 *
	 * @return boolean true.
	 */
	public function setShown($state)
	{
		$menu = $this->layout->menu;
		$menu->init();
		$state_store = $menu->getState();
		$state_store->show = (boolean)$state;
		$menu->setState($state_store);
		$menu->saveState();

		return true;
	}

	/**
	 * Sets the shown state of a section in the menu-view
	 *
	 * @param integer $section_id the identifier of the menu-view section to
	 *                             set the shown state for.
	 * @param boolean $state the shown state of the menu-view section.
	 *
	 * @return boolean true.
	 */
	public function setSectionShown($section_id, $state)
	{
		$menu = $this->layout->menu;
		$menu->init();
		$state_store = $menu->getState();
		if (isset($state_store->sections_show[$section_id])) {
			$state_store->sections_show[$section_id] = (boolean)$state;
			$menu->setState($state_store);
			$menu->saveState();
		}

		return true;
	}
}

?>
