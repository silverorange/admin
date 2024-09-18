<?php

/**
 * An orderable column
 *
 * @package   Admin
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminTableViewOrderableColumn extends SwatTableViewOrderableColumn
{


	public function displayHeader()
	{
		$this->link = $_GET['source'] ?? '';
		$this->unset_get_vars = ['source'];
		parent::displayHeader();
	}

}

?>
