<?php

require_once('Swat/SwatPage.php');
require_once('Admin/AdminMenu.php');

/**
 * Abstract base class for administrator pages.
 * @package Admin
 * @copyright silverorange 2004
 */
abstract class AdminPage extends SwatPage {

	/**
	 * Source of this page
	 * @var string
	 */
	public $source;

	/**
	 * Component name of this page
	 * @var string
	 */
	public $component;

	/**
	 * Subcomponent name of this page
	 * @var string
	 */
	public $subcomponent;

	abstract public function init();

	abstract public function display();
	
	abstract public function process();

	public function displayHeader() {
		/**
		 * TODO: pull in the real admin title, admin user name,
		 * and make these links work
		 */
		echo '<h1>', $this->app->title, '</h1>';
		echo '<div id="admin-syslinks">';
		echo 'Welcome <a href="Admin/Profile">Buckminster Fuller</a> &nbsp;|&nbsp;';
		echo '<a href="Admin/Profile">Customize</a> &nbsp;|&nbsp;';
		echo '<a href="Admin/Logout"><strong>Logout</strong></a>';
		echo '</div>';
	}

	public function displayMenu() {
		$sql_false = $this->app->db->quote(0, 'boolean');

		$sql = "SELECT admincomponents.shortname, admincomponents.title,
					admincomponents.section, adminsections.title AS sectiontitle,
					admincomponents.componentid,
					adminsubcomponents.title as subcomponent_title,
					adminsubcomponents.shortname as subcomponent_shortname
				FROM admincomponents 

				LEFT OUTER JOIN adminsubcomponents on
					adminsubcomponents.component = admincomponents.componentid

				INNER JOIN adminsections ON
					admincomponents.section = adminsections.sectionid

				WHERE adminsections.hidden = {$sql_false}
				
				AND admincomponents.hidden = {$sql_false}

				AND (
					adminsubcomponents.hidden = {$sql_false}
					OR adminsubcomponents.hidden is  null
				)";
		// TODO: make this work once sessions are working
				/*
				AND adminarticles.articleid IN (
					SELECT article
					FROM adminarticle_admingroup
					INNER JOIN adminuser_admingroup ON
						adminarticle_admingroup.groupnum = adminuser_admingroup.groupnum
					WHERE adminuser_admingroup.usernum = ".$_SESSION['userID']."
				)
				*/
		$sql.="	ORDER BY adminsections.displayorder, adminsections.title,
				admincomponents.section, admincomponents.displayorder,
				admincomponents.title, adminsubcomponents.displayorder,
				adminsubcomponents.title";
		
		$types = array('text', 'text', 'integer', 'text', 'integer', 'text', 'text');
		$menu = $this->app->db->query($sql, $types, true, 'AdminMenu');
		$menu->display();	

	}
	
}
