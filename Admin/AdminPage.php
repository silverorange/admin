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

	public function displayMenu($app) {
		$sql="SELECT adminarticles.shortname, adminarticles.title,
					adminarticles.section, adminsections.title AS sectiontitle
				FROM adminarticles 
				INNER JOIN adminsections ON
					adminarticles.section = adminsections.sectionid
				WHERE adminsections.hidden='0' --$app->db->quote(0,'bit')
				
				AND adminarticles.hidden='0' --$app->db->quote(0,'bit')
				/*
				AND adminarticles.articleid IN (
					SELECT article
					FROM adminarticle_admingroup
					INNER JOIN adminuser_admingroup ON
						adminarticle_admingroup.groupnum = adminuser_admingroup.groupnum
					WHERE adminuser_admingroup.usernum = ".$_SESSION['userID']."
				)
				*/
				ORDER BY adminsections.displayorder, adminsections.title,
				adminarticles.section, adminarticles.displayorder,
				adminarticles.title";
		
		$types = array('text', 'text', 'integer','text');
		$result = $app->db->query($sql, $types);
		
		if (MDB2::isError($result)) 
			throw new Exception($result->getMessage());

		$section_out=0;
		$currentrow=0;			
		
		echo '<ul>';
		while ($row = $result->fetchRow(MDB2_FETCHMODE_OBJECT)) {
			$currentrow++;
			if ($row->section != $section_out) {
				if ($currentrow!=1) echo "</ul>";
				echo '<li><span>'.$row->sectiontitle.'</span></li>';
				echo '<ul>';
			}
			$section_out=$row->section;
			echo '<li><a href="?source='.$row->shortname.'">';
			echo $row->title;
			echo '</a></li>';
		}
		echo '</ul></ul>';
	}
	
	abstract public function init($app);

	abstract public function display($app);
	
	abstract public function process($app);
}
