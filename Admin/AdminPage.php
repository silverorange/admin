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
		
		
		while ($row = $result->fetchRow(MDB2_FETCHMODE_OBJECT)) {
			$currentrow++;
			if ($row->section != $section_out) {
				if ($currentrow!=1) echo "</table></DIV>";
				?>
				<div ID="el<?=$currentrow?>Parent" class="parent"><table border="0" cellpadding="0" cellspacing="0"><tr><td valign="top"><a href="#" onClick="expandIt('el<?=$currentrow?>'); return false;"><img name="imEx" src="images/m_open.gif" border="0" alt="Expand/Collapse Item" height="20" width="20"></a></td>
				<td class="titles"><img src="images/block.gif" height="8" width="1"><br><b><a href="sub.php?section=<?=$row->section?>" class="sectionlinks"><?=$row->sectiontitle?></a></b></td></tr></table></DIV>
				<div ID="el<?=$currentrow?>Child" class="child"
				<table border="0" cellpadding="1" cellspacing="1">
				<tr><td colspan="2"><img src="images/block.gif" height="1" width="1"></td></tr>
				<?
			}
			$section_out=$row->section;
			?>
			<tr>
				<td align="right" valign="top" class="bullet"><img src="images/block.gif" height="2" width="2" hspace="10">&#149;</td>
				<td><a href="sub.php?source=<?=$row->shortname?>" class="articlelinks"><b><?=$row->title?></b></a></td>
			</tr>
			<?
		}
		echo "</table></DIV>";
	}
	
	abstract public function init($app);

	abstract public function display($app);
	
	abstract public function process($app);
}
