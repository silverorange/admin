<?php

require_once("Admin/AdminPage.php");
require_once('Admin/AdminUI.php');
require_once("MDB2.php");

/**
 * Edit page for AdminSections
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminSectionsEdit extends AdminPage {

	private $ui;

	public function init() {
		$this->ui = new AdminUI();
		$this->ui->loadFromXML('Admin/AdminSections/edit.xml');
	}

	public function display() {
		$id = intval(SwatApplication::initVar('id'));
		$btn_submit = $this->ui->getWidget('btn_submit');
		$frame = $this->ui->getWidget('frame');

		if ($id == 0) {
			$btn_submit->setTitleFromStock('create');
			$frame->title = 'New Section';
		} else {
			$this->loadData($id);
			$btn_submit->setTitleFromStock('apply');
		}
				
		$form = $this->ui->getWidget('editform');
		$form->action = $this->source;
		$form->addHiddenField('id', $id);
		
		$order = $this->ui->getWidget('myorder');
		//$order->height = 500;
		//$order->width = '150';
		$myarray = array();
		$myarray[1] = 'Element 1';
		$myarray[2] = 'Element 2';
		$myarray[3] = 'Element 3';
		$myarray[4] = 'Element 4';
		$myarray[5] = 'Element 5';
		$myarray[6] = 'Element 6';
		$myarray[7] = 'Element 7';
		$myarray[8] = 'Element 8';
		$myarray[9] = 'Element 9';
		$myarray[10] = 'Element 10';
		$myarray['a'] = 'Element String Key a';
		$myarray['b'] = 'Element String Key b';
		$myarray['c'] = 'Element String Key c';
		$myarray['d'] = 'Element String Key d';
		$myarray['e'] = 'Element String Key e';
		$myarray['img1'] = '<img src="http://gallery.whitelands.com/files/tiny/photo7784.jpg"> Element Test Image 1';
		$myarray['img2'] = '<img src="http://gallery.whitelands.com/files/tiny/photo142.jpg"> Element Test Image 2';
		$myarray['img3'] = '<img src="http://gallery.whitelands.com/files/tiny/photo75.jpg"> Element Test Image 3';
		$myarray['img4'] = '<img src="http://gallery.whitelands.com/files/tiny/photo33011.jpg"> Element Test Image 4';
		$myarray['img5'] = '<img src="http://gallery.whitelands.com/files/tiny/photo32720.jpg"> Element Test Image 5';
		
		$order->value = $myarray;

		$root = $this->ui->getRoot();
		$root->display();
	}

	public function process() {
		$form = $this->ui->getWidget('editform');
		$id = intval(SwatApplication::initVar('id'));

		if ($form->process()) {
			if (!$form->hasErrorMessage()) {
				$this->saveData($id);
				$this->app->relocate($this->component);
			}
		}
	}

	private function saveData($id) {
		$db = $this->app->db;

		if ($id == 0)
			$sql = 'INSERT INTO adminsections(title, hidden, description)
				VALUES (%s, %s, %s)';
		else
			$sql = 'UPDATE adminsections
				SET title = %s,
					hidden = %s,
					description = %s
				WHERE sectionid = %s';

		$sql = sprintf($sql,
			$db->quote($this->ui->getWidget('title')->value, 'text'),
			$db->quote($this->ui->getWidget('hidden')->value, 'boolean'),
			$db->quote($this->ui->getWidget('description')->value, 'text'),
			$db->quote($id, 'integer'));

		$db->query($sql);
	}

	private function loadData($id) {
		$sql = 'SELECT title, hidden, description
			FROM adminsections WHERE sectionid = %s';

		$sql = sprintf($sql,
			$this->app->db->quote($id, 'integer'));

		$rs = $this->app->db->query($sql, array('text', 'boolean', 'text'));
		$row = $rs->fetchRow(MDB2_FETCHMODE_OBJECT);

		$this->ui->getWidget('title')->value = $row->title;
		$this->ui->getWidget('hidden')->value = $row->hidden;
		$this->ui->getWidget('description')->value = $row->description;
	}
}
?>
