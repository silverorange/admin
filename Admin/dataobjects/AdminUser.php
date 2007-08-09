<?php

require_once 'Swat/SwatString.php';
require_once 'SwatDB/SwatDBDataObject.php';
require_once 'Admin/dataobjects/AdminUserHistoryWrapper.php';

/**
 * User account for an admin
 *
 * @package   Admin
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       AdminGroup
 */
class AdminUser extends SwatDBDataObject
{
	// {{{ class constants

	/**
	 * Length of salt value used to protect user's passwords from dictionary
	 * attacks
	 */
	const PASSWORD_SALT_LENGTH = 16;

	// }}}
	// {{{ public properties

	/**
	 * Unique identifier
	 *
	 * @var integer
	 */
	public $id;

	/**
	 * Email address of this user
	 *
	 * @var string
	 */
	public $email;

	/**
	 * Full name of this user
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Hashed version of this user's salted password
	 *
	 * @var string
	 */
	public $password;

	/**
	 * The salt value used to protect this user's password
	 *
	 * @var string
	 */
	public $password_salt;

	/**
	 * Token used for password regeneration for this user
	 *
	 * This field is usually null unless this user is being forced to reset
	 * his or her password.
	 *
	 * @var string
	 */
	public $password_tag;

	/**
	 * Whether or not this user will be forced to change his or her password
	 * upon login
	 *
	 * @var boolean
	 */
	public $force_change_password;

	/**
	 * Whether or not this user is enabled
	 *
	 * Users that are not enabled will not be able to login to the admin.
	 *
	 * @var boolean
	 */
	public $enabled;

	/**
	 * Serialized menu state for this user
	 *
	 * This is a serialized instance of an AdminMenuStateStore object.
	 *
	 * @var string
	 *
	 * @see AdminMenuView
	 * @see AdminMenuStateStore
	 */
	public $menu_state;

	// }}}
	// {{{ public function isAuthenticated()

	/**
	 * Checks if a user is authenticated
	 *
	 * After a user's username and password have been verified, perform
	 * additional checks on the user's authentification. This method is run
	 * on every page load, not just at login, to ensure the user has
	 * permission to access the admin.
	 *
	 * @return boolean True if the user has authenticated access to the
	 *                 admin. 
	 */
	public function isAuthenticated()
	{
		return ($this->force_change_password === false);
	}

	// }}}
	// {{{ public function hasAccess()

	/**
	 * Gets whether or not this user has access to the given component
	 *
	 * @param AdminComponent $component the component to check.
	 *
	 * @return boolean true if this used has access to the given component and
	 *                  false if this used does not have access to the given
	 *                  component.
	 */
	public function hasAccess(AdminComponent $component)
	{
		$this->checkDB();

		$sql = sprintf('select %s in (
			select component from AdminComponentAdminGroupBinding
				inner join AdminUserAdminGroupBinding on
					AdminComponentAdminGroupBinding.groupnum =
						AdminUserAdminGroupBinding.groupnum and
							AdminUserAdminGroupBinding.usernum = %s)',
			$this->db->quote($component->id, 'integer'),
			$this->db->quote($this->id, 'integer'));

		return SwatDB::queryOne($this->db, $sql);
	}

	// }}}
	// {{{ public function setPassword()

	/**
	 * Sets this user's password
	 *
	 * This method takes care of automatically salting and hashing the password.
	 * The new password is not saved until this user is saved.
	 *
	 * @param string $password the user's new password.
	 */
	public function setPassword($password)
	{
		$this->password_salt = SwatString::getSalt(self::PASSWORD_SALT_LENGTH);
		$this->password = md5($password.$this->password_salt);
	}

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'AdminUser';
		$this->id_field = 'integer:id';
	}

	// }}}
	// {{{ protected function loadHistory()

	/**
	 * Gets user history for this user
	 *
	 * @return AdminUserHistoryWrapper a set of {@link AdminUserHistory}
	 *                                  objects containing this admin user's
	 *                                  login history.
	 */
	protected function loadHistory()
	{
		$sql = sprintf('select * from AdminUserHistory where usernum = %s',
			$this->db->quote($this->id, 'integer'));

		return SwatDB::query($this->db, $sql, 'AdminUserHistoryWrapper');
	}

	// }}}
}

?>
