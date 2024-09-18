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


	const GETTEXT_DOMAIN = 'admin';



	/**
	 * Whether or not this package is initialized
	 *
	 * @var boolean
	 */
	private static $is_initialized = false;



	public static function _($message)
	{
		return self::gettext($message);
	}



	public static function gettext($message)
	{
		return dgettext(self::GETTEXT_DOMAIN, $message);
	}



	public static function ngettext(
		$singular_message,
		$plural_message,
		$number
	) {
		return dngettext(self::GETTEXT_DOMAIN,
			$singular_message, $plural_message, $number);
	}



	public static function setupGettext()
	{
		bindtextdomain(self::GETTEXT_DOMAIN, '@DATA-DIR@/Admin/locale');
		bind_textdomain_codeset(self::GETTEXT_DOMAIN, 'UTF-8');
	}



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
		return [
			'admin.allow_reset_password' => '0',
			'admin.two_fa_enabled' => false,
		];
	}



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



	/**
	 * Prevent instantiation of this static class
	 */
	private function __construct()
	{
	}

}

?>
