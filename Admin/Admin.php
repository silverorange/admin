<?php

/**
 * Container for package wide static methods
 *
 * @package   Admin
 * @copyright 2005-2017 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Admin
{
	// {{{ constants

	const GETTEXT_DOMAIN = 'admin';

	// }}}
	// {{{ private properties

	/**
	 * Whether or not this package is initialized
	 *
	 * @var boolean
	 */
	private static $is_initialized = false;

	// }}}
	// {{{ public static function _()

	public static function _($message)
	{
		return self::gettext($message);
	}

	// }}}
	// {{{ public static function gettext()

	public static function gettext($message)
	{
		return dgettext(self::GETTEXT_DOMAIN, $message);
	}

	// }}}
	// {{{ public static function ngettext()

	public static function ngettext(
		$singular_message,
		$plural_message,
		$number
	) {
		return dngettext(self::GETTEXT_DOMAIN,
			$singular_message, $plural_message, $number);
	}

	// }}}
	// {{{ public static function setupGettext()

	public static function setupGettext()
	{
		bindtextdomain(self::GETTEXT_DOMAIN, '@DATA-DIR@/Admin/locale');
		bind_textdomain_codeset(self::GETTEXT_DOMAIN, 'UTF-8');
	}

	// }}}
	// {{{ public static function getConfigDefinitions()

	/**
	 * Gets configuration definitions used by the Admin package
	 *
	 * Applications should add these definitions to their config module before
	 * loading the application configuration.
	 *
	 * @return array the configuration definitions used by the Admin package.
	 *
	 * @see SiteConfigModule::addDefinitions()
	 */
	public static function getConfigDefinitions()
	{
		return array(
			'admin.allow_reset_password' => '0',
		);
	}

	// }}}
	// {{{ public static function init()

	public static function init()
	{
		if (self::$is_initialized) {
			return;
		}

		Swat::init();
		Site::init();

		self::setupGettext();

		SwatUI::mapClassPrefixToPath('Admin', 'Admin');

		self::$is_initialized = true;
	}

	// }}}
	// {{{ private function __construct()

	/**
	 * Prevent instantiation of this static class
	 */
	private function __construct()
	{
	}

	// }}}
}

?>
