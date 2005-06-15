<?php

/**
 * Container for package wide static methods
 *
 * @package   Admin
 * @copyright 2005 silverorange
 */
class Admin
{
	const GETTEXT_DOMAIN = 'admin';

	function _($message)
	{
		return Admin::gettext($message);
	}

	function gettext($message)
	{
		return dgettext(Admin::GETTEXT_DOMAIN, $message);
	}

	function ngettext($singular_message, $plural_message, $number)
	{
		return dngettext(Admin::GETTEXT_DOMAIN, $singular_message, $plural_message, $number);
	}

}

?>
