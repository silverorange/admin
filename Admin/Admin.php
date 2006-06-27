<?php

/**
 * Container for package wide static methods
 *
 * @package   Admin
 * @copyright 2005 silverorange
 */
class Admin
{
	// {{{ constants

	const GETTEXT_DOMAIN = 'admin';

	// }}}
	// {{{ public static function _()

	public static function _($message)
	{
		return Admin::gettext($message);
	}

	// }}}
	// {{{ public static function gettext()

	public static function gettext($message)
	{
		return dgettext(Admin::GETTEXT_DOMAIN, $message);
	}

	// }}}
	// {{{ public static function ngettext()

	public static function ngettext($singular_message,
		$plural_message, $number)
	{
		return dngettext(Admin::GETTEXT_DOMAIN,
			$singular_message, $plural_message, $number);
	}

	// }}}
}

?>
