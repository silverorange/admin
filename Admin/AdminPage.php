<?php
/**
 * @package Admin
 * @copyright silverorange 2004
 */
require_once('Swat/SwatPage.php');

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
		echo 'Welcome <a href="#">Buckminster Fuller</a> &nbsp;|&nbsp;
			<a href="#">Customize</a> &nbsp;|&nbsp;
			<a href="#"><strong>Logout</strong></a>
			</div>';
	}

	public function displayMenu($app) {
		$sql = "SELECT admincomponents.shortname, admincomponents.title,
					admincomponents.section, adminsections.title AS sectiontitle
				FROM admincomponents 
				INNER JOIN adminsections ON
					admincomponents.section = adminsections.sectionid
				WHERE adminsections.hidden='0' --$app->db->quote(0,'bit')
				
				AND admincomponents.hidden='0' --$app->db->quote(0,'bit')
		";
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
		$sql.="
				ORDER BY adminsections.displayorder, adminsections.title,
				admincomponents.section, admincomponents.displayorder,
				admincomponents.title";
		
		$types = array('text', 'text', 'integer','text');
		$result = $app->db->query($sql, $types);
		
		if (MDB2::isError($result)) 
			throw new Exception($result->getMessage());

		$section_out = 0;
		$currentrow = 0;		
		
		echo '<ul>';
		while ($row = $result->fetchRow(MDB2_FETCHMODE_OBJECT)) {
			$currentrow++;
			if ($row->section != $section_out) {
				if ($currentrow != 1) echo "</li></ul>";
				echo '<li><span>'.$row->sectiontitle.'</span>';
				echo '<ul>';
			}
			$section_out = $row->section;
			echo '<li><a href="a/'.$row->shortname.'">';
			echo $row->title;
			echo '</a></li>';
		}
		echo '</ul></li></ul>';
	}
	
	abstract public function init($app);

	abstract public function display($app);
	
	abstract public function process($app);
}
