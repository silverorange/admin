<?php

require_once 'Admin/exceptions/AdminException.php';

/**
 * Base class for exceptions that are thrown in response to user input and
 * should be handled gracefully.
 *
 * @package   Admin
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminUserException extends AdminException
{
	// {{{ public properties

	public $title = null;

	// }}}
}

?>
