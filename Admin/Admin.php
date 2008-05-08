<?php

require_once 'Swat/Swat.php';
require_once 'Site/Site.php';

/**
 * Container for package wide static methods
 *
 * @package   Admin
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Admin
{
	// {{{ constants

	/**
	 * The package identifier
	 */
	const PACKAGE_ID = 'Admin';

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
	// {{{ public static function setupGettext()

	public static function setupGettext()
	{
		bindtextdomain(Admin::GETTEXT_DOMAIN, '@DATA-DIR@/Admin/locale');
		bind_textdomain_codeset(Admin::GETTEXT_DOMAIN, 'UTF-8');
	}

	// }}}
	// {{{ public static function getDependencies()

	/**
	 * Gets the packages this package depends on
	 *
	 * @return array an array of package IDs that this package depends on.
	 */
	public static function getDependencies()
	{
		return array(Swat::PACKAGE_ID, Site::PACKAGE_ID);
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
}

Admin::setupGettext();
SwatUI::mapClassPrefixToPath('Admin', 'Admin');

?>
