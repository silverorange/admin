<?php

/**
 * An exception in Admin
 *
 * @package   Admin
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminException extends SwatException
{


	public function __construct($message = null, $code = 0)
	{
		if (is_object($message) && ($message instanceof PEAR_Error)) {
			$error = $message;
			$message = $error->getMessage();
			$message.= "\n".$error->getUserInfo();
			$code = $error->getCode();
		}

		parent::__construct($message, $code);
	}


}

?>
