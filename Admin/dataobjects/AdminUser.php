<?php

require_once 'Swat/SwatString.php';
require_once 'SwatDB/SwatDBDataObject.php';
require_once 'Admin/dataobjects/AdminUserHistoryWrapper.php';
require_once 'Admin/AdminResetPasswordMailMessage.php';
require_once 'Text/Password.php';

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
	 * Checks if a user is authenticated for an admin application
	 *
	 * After a user's username and password have been verified, perform
	 * additional checks on the user's authentication. This method should be
	 * checked on every page load -- not just at login -- to ensure the user
	 * has permission to access the specified admin application.
	 *
	 * @param AdminApplication $app the application to authenticate this user
	 *                               against.
	 *
	 * @return boolean true if this user has authenticated access to the
	 *                 admin and false if this user does not.
	 */
	public function isAuthenticated(AdminApplication $app)
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
		$salt = SwatString::getSalt(self::PASSWORD_SALT_LENGTH);
		$this->password_salt = base64_encode($salt);
		$this->password = md5($password.$salt);
	}

	// }}}
	// {{{ public function resetPassword()

	/**
	 * Resets this user's password
	 *
	 * Creates a unique tag and emails this user's holder a tagged URL to
	 * update his or her password.
	 *
	 * @param AdminApplication $app the application resetting this account's
	 *                              password.
	 *
	 * @return string $password_tag a hashed tag to verify the account
	 */
	public function resetPassword(AdminApplication $app)
	{
		$this->checkDB();

		$password_tag = SwatString::hash(uniqid(rand(), true));

		/*
		 * Update the database with new password tag. Don't use the regular
		 * dataobject saving here in case other fields have changed.
		 */
		$id_field = new SwatDBField($this->id_field, 'integer');
		$sql = sprintf('update %s set password_tag = %s where %s = %s',
			$this->table,
			$this->db->quote($password_tag, 'text'),
			$id_field->name,
			$this->db->quote($this->{$id_field->name}, $id_field->type));

		SwatDB::exec($this->db, $sql);

		return $password_tag;
	}

	// }}}
	// {{{ public function sendResetPasswordMailMessage()

	/**
	 * Emails this user's holder with instructions on how to finish
	 * resetting his or her password
	 *
	 * @param AdminApplication $app the application sending mail.
	 * @param string $password_link a URL indicating the page at which the
	 *                               account holder may complete the reset-
	 *                               password process.
	 *
	 * @see AdminUser::resetPassword()
	 */
	public function sendResetPasswordMailMessage(
		AdminApplication $app, $password_link)
	{
		$reset_email = new AdminResetPasswordMailMessage($app, $this,
			$password_link, $app->title);

		$reset_email->smtp_server  = $app->config->email->smtp_server;
		$reset_email->from_address = $app->config->email->service_address;
		$reset_email->from_name    = $app->title;
		$reset_email->subject      = 'Reset Your '.$app->title.' Password';

		$reset_email->send();
	}

	// }}}
	// {{{ public function loadFromEmail()

	/**
	 * Loads a user from the database with just an email address
	 *
	 * This is useful for password recovery and email address verification.
	 *
	 * @param string $email the email address of the user.
	 * @param SiteInstance $instance Optional site instance for this user.
	 *                               Used when the site implements
	 *                               {@link SiteMultipleInstanceModule}.
	 *
	 * @return boolean true if the loading was successful and false if it was
	 *                  not.
	 */
	public function loadFromEmail($email, SiteInstance $instance = null)
	{
		$this->checkDB();

		$sql = sprintf('select id from %s
			where lower(email) = lower(%s)',
			$this->table,
			$this->db->quote($email, 'text'));

//		if ($instance !== null)
//			$sql.= sprintf(' and instance = %s',
//				$this->db->quote($instance->id, 'integer'));

		$id = SwatDB::queryOne($this->db, $sql);

		if ($id === null)
			return false;

		return $this->load($id);
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
