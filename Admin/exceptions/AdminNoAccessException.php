<?php

require_once 'Admin/exceptions/AdminException.php';

/**
 * Thrown when access to a page is not allowed
 *
 * @package   Admin
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminNoAccessException extends AdminException
{
	/**
	 * Creates a new no access exception
	 *
	 * @param string $message the message of the exception.
	 * @param integer $code the code of the exception.
	 */
	public function __construct($message = null, $code = 0)
	{
		parent::__construct($message, $code);
		$this->title = _('No Access');
	}
}

?>
