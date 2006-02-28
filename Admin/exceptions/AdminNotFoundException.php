<?php

require_once 'Admin/exceptions/AdminUserException.php';

/**
 * Thrown when something is not found
 *
 * @package   Admin
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminNotFoundException extends AdminUserException
{
	/**
	 * Creates a new not found exception
	 *
	 * @param string $message the message of the exception.
	 * @param integer $code the code of the exception.
	 */
	public function __construct($message = null, $code = 0)
	{
		parent::__construct($message, $code);
		$this->title = Admin::_('Not Found');
	}
}

?>
