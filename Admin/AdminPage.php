<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatPage.php');
require_once('Admin/AdminMenu.php');

/**
 * Abstract base class for admin pages.
 */
abstract class AdminPage extends SwatPage {

	/**
	 * @var title Title of the page.
	 */
	public $title = '';

	function __construct() {

	}

	public function displayHeader($app) {
		/**
		 * TODO: pull in the real admin title, admin user name,
		 * and make these links work
		 */
		echo '<h1>Example Admin</h1>';
		echo '<div id="admin-syslinks">';
		echo 'Welcome <a href="#">Buckminster Fuller</a> &nbsp;|&nbsp;';
		echo '<a href="#">Customize</a> &nbsp;|&nbsp;';
		echo '<a href="#"><strong>Logout</strong></a>';
		echo '</div>';
	}

	public function displayMenu($app) {
		$sql_false = $app->db->quote(0, 'boolean');

		$sql = "SELECT admincomponents.shortname, admincomponents.title,
					admincomponents.section, adminsections.title AS sectiontitle,
					admincomponents.componentid,
					adminsubcomponents.title as subcomponent_title,
					adminsubcomponents.shortname as subcomponent_shortname
				FROM admincomponents 

				LEFT OUTER JOIN adminsubcomponents on
					adminsubcomponents.subcomponentid = admincomponents.componentid

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
		$menu = $app->db->query($sql, $types, true, 'AdminMenu');
		$menu->display();	

	}
	
	abstract public function init($app);

	abstract public function display($app);
	
	abstract public function process($app);
}
