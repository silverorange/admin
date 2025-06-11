<?php

/**
 * Thrown when access to a page is not allowed
 *
 * @package   Admin
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminNoAccessException extends AdminUserException
{


	/**
	 * The user that was denied access
	 *
	 * @var AdminUser
	 */
	protected $user;




	/**
	 * Creates a new no access exception
	 *
	 * @param string $message the message of the exception.
	 * @param integer $code the code of the exception.
	 * @param AdminUser $user optional. The user that was denied access.
	 */
	public function __construct(
		$message = null,
		$code = 0,
		AdminUser $user = null
	) {
		parent::__construct($message, $code);
		$this->user = $user;
		$this->title = Admin::_('No Access');
	}




	/**
	 * Gets the user that was denied access
	 *
	 * @return AdminUser the user that was denied access.
	 */
	public function getUser()
	{
		return $this->user;
	}


}

?>
