<?php

require_once 'Swat/exceptions/SwatException.php';

/**
 * An exception in Admin
 *
 * @package   Admin
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminException extends SwatException
{
	public $title = null;
}
