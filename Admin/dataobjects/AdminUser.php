<?php

/**
 * User account for an admin
 *
 * @package   Admin
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       AdminGroup
 */
class AdminUser extends SwatDBDataObject
{
	// {{{ class constants

	/**
	 * The number of days after which a user is considered inactive
	 *
	 * If a user has no sign-in activity for this many days, it will be
	 * prevented from signing into the admin.
	 *
	 * @see AdminUser::isActive()
	 */
	const EXPIRY_DAYS = 90;

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
	 * Date when the reset password tag was created
	 *
	 * This is used to expire old reset password requests.
	 *
	 * @var SwatDate
	 */
	public $password_tag_date;

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

	/**
	 * Whether or not this user has access to all instances
	 *
	 * Only relevent on a multiple instance site. This allows the user to login
	 * to all instance admins regardless of AdminUserInstanceBindings. Also
	 * this user can login into a master admin loading with a null instance.
	 *
	 * @var boolean
	 */
	public $all_instances;

	/**
	 * Date when the user account was activated.
	 *
	 * @var SwatDate
	 */
	public $activation_date;

	/**
	 * Google 2FA Secret
	 *
	 * @var string
	 */
	public $google_2fa_secret;

	/**
	 * Google 2FA Enabled
	 *
	 * @var boolean
	 */
	public $google_2fa_enabled = false;

	/**
	 * Google 2FA Timestamp
	 *
	 * @var integer
	 */
	public $google_2fa_timestamp;

	// }}}
	// {{{ protected properties

	/**
	 * @var SiteInstance
	 */
	protected $instance;

	/**
	 * @var boolean
	 */
	protected $google_2fa_authenticated = false;

	// }}}
	// {{{ public function isAuthenticated()

	/**
	 * Checks if a user is authenticated for an admin application
	 *
	 * After a user's username and password have been verified, perform
	 * additional checks on the user's authentication. This method should be
	 * checked on every page load -- not just at login -- to ensure the user
	 * has permission to access the specified admin application. It also checks
	 * whether or not this user belongs to the current site instance as well as
	 * well as performing all regular checks.
	 *
	 * @param AdminApplication $app the application to authenticate this user
	 *                               against.
	 *
	 * @return boolean true if this user has authenticated access to the
	 *                 admin and false if this user does not.
	 */
	public function isAuthenticated(AdminApplication $app)
	{
		$authenticated = false;

		// Only validate agains instances if site is actually using the
		// instance module.
		if ($app->hasModule('SiteMultipleInstanceModule')) {
			if ($this->all_instances) {
				// This user has acess to all instances
				$authenticated = true;
			} else {
				// Ensure the admin user has a binding to the current instance.
				$instance = $app->getModule('SiteMultipleInstanceModule');
				$instance_id = $instance->getId();

				if ($instance_id !== null &&
					isset($this->instances[$instance_id])) {

					$authenticated = true;
				}
			}

			// Make sure instance is set so activation check works properly.
			if ($app->getInstance() instanceof SiteInstance) {
				$this->setInstance($app->getInstance());
			}
		} else {
			$authenticated = true;
		}

		$authenticated = (
			$authenticated &&
			$this->isActive() &&
			!$this->force_change_password &&
			(
				$app->isGoogle2faEnabled() && (
					!$this->google_2fa_enabled ||
					$this->google_2fa_authenticated
				)
			)
		);

		return $authenticated;
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
	// {{{ public function hasAccessByShortname()

	/**
	 * Gets whether or not this user has access to the given component
	 *
	 * @param string $shortname the shortname of the component to check
	 *
	 * @return boolean true if this used has access to the given component and
	 *                  false if this used does not have access to the given
	 *                  component.
	 */
	public function hasAccessByShortname($shortname)
	{
		$this->checkDB();

		$sql = sprintf('select id from AdminComponent
				inner join AdminComponentAdminGroupBinding on
					AdminComponent.id =
						AdminComponentAdminGroupBinding.component
				inner join AdminUserAdminGroupBinding on
					AdminComponentAdminGroupBinding.groupnum =
						AdminUserAdminGroupBinding.groupnum and
							AdminUserAdminGroupBinding.usernum = %s
			where shortname = %s',
			$this->db->quote($this->id, 'integer'),
			$this->db->quote($shortname, 'text'));

		return SwatDB::queryOne($this->db, $sql);
	}

	// }}}
	// {{{ public function setPasswordHash()

	/**
	 * Sets this account's password hash
	 *
	 * @param string $password_hash the password hash for this account.
	 */
	public function setPasswordHash($password_hash)
	{
		$this->password = $password_hash;

		// Note: AdminUser now uses crypt() for password hashing. The salt
		// is stored in the same field as the hashed password.
		$this->password_salt = null;
	}

	// }}}
	// {{{ public function resetPassword()

	/**
	 * Resets this user's password
	 *
	 * Creates a unique tag enabling this user to reset his/her own password.
	 * The password tag is saved for this user and should be sent to this user
	 * in an email with further instructions.
	 *
	 * @return string $password_tag a unique tag to verify the account when
	 *                               resetting the password.
	 *
	 * @see AdminResetPasswordMailMessage
	 */
	public function resetPassword()
	{
		$this->checkDB();

		$password_tag = SwatString::hash(uniqid(rand(), true));
		$now = new SwatDate();
		$now->toUTC();

		/*
		 * Update the database with new password tag. Don't use the regular
		 * dataobject saving here in case other fields have changed.
		 */
		$id_field = new SwatDBField($this->id_field, 'integer');
		$sql = sprintf('update %s set password_tag = %s, password_tag_date = %s
			where %s = %s',
			$this->table,
			$this->db->quote($password_tag, 'text'),
			$this->db->quote($now->getDate(), 'date'),
			$id_field->name,
			$this->db->quote($this->{$id_field->name}, $id_field->type));

		SwatDB::exec($this->db, $sql);

		return $password_tag;
	}

	// }}}
	// {{{ public function loadFromEmail()

	/**
	 * Loads a user from the database with just an email address
	 *
	 * This is useful for password recovery and email address verification.
	 *
	 * @param string $email the email address of the user.
	 *
	 * @return boolean true if the loading was successful and false if it was
	 *                  not.
	 */
	public function loadFromEmail($email)
	{
		$this->checkDB();

		$sql = sprintf('select id from %s
			where lower(email) = lower(%s)',
			$this->table,
			$this->db->quote($email, 'text'));

		$id = SwatDB::queryOne($this->db, $sql);

		if ($id === null)
			return false;

		return $this->load($id);
	}

	// }}}
	// {{{ public function setInstance()

	/**
	 * Sets the instance to use when loading instance-specific information for
	 * this user.
	 *
	 * @param SiteInstance $instance the instance to use.
	 */
	public function setInstance(SiteInstance $instance)
	{
		$this->instance = $instance;
	}

	// }}}
	// {{{ public function isActive()

	/**
	 * Checks to see if this user is active
	 *
	 * Users are inactive if they haven't logged in or been activated in the
	 * last 90 days.
	 *
	 * @return boolean
	 *
	 * @see AdminUser::EXPIRY_DAYS
	 */
	public function isActive()
	{
		$is_active = false;

		$comparison_date = null;
		$comparison_dates = array();

		if ($this->most_recent_history instanceof AdminUserHistory) {
			$comparison_dates[] = $this->most_recent_history->login_date;
		}

		if ($this->activation_date instanceof SwatDate) {
			$comparison_dates[] = $this->activation_date;
		}

		// Get the most recent activity date (either user history or activation
		// date)
		foreach ($comparison_dates as $date) {
			if (!$comparison_date instanceof SwatDate ||
				$date->after($comparison_date)) {
				$comparison_date = $date;
			}
		}

		$threshold = new SwatDate();
		$threshold->subtractDays(static::EXPIRY_DAYS);
		if ($comparison_date instanceof SwatDate &&
			$comparison_date->after($threshold)) {
			$is_active = true;
		}

		return $is_active;
	}

	// }}}
	// {{{ public function setGoogle2faAuthenticated()

	public function setGoogle2faAuthenticated($authenticated = true)
	{
		$this->google_2fa_authenticated = $authenticated;
	}

	// }}}
	// {{{ public function isGoogle2faAuthenticated()

	public function isGoogle2faAuthenticated()
	{
		return $this->google_2fa_authenticated;
	}

	// }}}
	// {{{ protected function init()

	protected function init()
	{
		$this->table = 'AdminUser';
		$this->id_field = 'integer:id';

		$this->registerDateProperty('password_tag_date');
		$this->registerDateProperty('activation_date');
	}

	// }}}
	// {{{ protected function loadHistory()

	/**
	 * Gets user history for this user
	 *
	 * If account instance is set with the {@link AdminUser::setInstance()}
	 * method, history will be limited to that instance.
	 *
	 * @return AdminUserHistoryWrapper a set of {@link AdminUserHistory}
	 *                                 objects containing this admin user's
	 *                                 login history.
	 *
	 * @see AdminUser::setInstance()
	 */
	protected function loadHistory()
	{
		$instance_id = null;

		if ($this->instance instanceof SiteInstance) {
			$instance_id = $this->instance->getId();
		}

		$sql = sprintf(
			'select * from AdminUserHistory
			where usernum = %s and instance %s %s
			order by login_date desc',
			$this->db->quote($this->id, 'integer'),
			SwatDB::equalityOperator($instance_id),
			$this->db->quote($instance_id, 'integer')
		);

		return SwatDB::query($this->db, $sql, 'AdminUserHistoryWrapper');
	}

	// }}}
	// {{{ protected function loadMostRecentHistory()

	/**
	 * Gets most recent login history for this user
	 *
	 * If account instance is set with the {@link AdminUser::setInstance()}
	 * method, history will be limited to that instance.
	 *
	 * @return AdminUserHistory a {@link AdminUserHistory} containing
	 *                          this admin user's most recent login history.
	 *
	 * @see AdminUser::setInstance()
	 */
	protected function loadMostRecentHistory()
	{
		$instance_id = null;

		if ($this->instance instanceof SiteInstance) {
			$instance_id = $this->instance->getId();
		}

		$sql = sprintf(
			'select * from AdminUserHistory
			where usernum = %s and instance %s %s
			order by login_date desc',
			$this->db->quote($this->id, 'integer'),
			SwatDB::equalityOperator($instance_id),
			$this->db->quote($instance_id, 'integer')
		);

		$this->db->setLimit(1);

		return SwatDB::query(
			$this->db,
			$sql,
			'AdminUserHistoryWrapper'
		)->getFirst();
	}

	// }}}
	// {{{ protected function loadInstances()

	/**
	 * Load the Instances that this user has access to
	 *
	 * @return SiteInstanceWrapper the site instances this user belongs to.
	 */
	protected function loadInstances()
	{
		$sql = sprintf('select Instance.*
				from Instance
				inner join AdminUserInstanceBinding on
					AdminUserInstanceBinding.instance = Instance.id
				where AdminUserInstanceBinding.usernum = %s',
			$this->db->quote($this->id, 'integer'));

		$wrapper_class = SwatDBClassMap::get('SiteInstanceWrapper');
		return SwatDB::query($this->db, $sql, $wrapper_class);
	}

	// }}}
	// {{{ protected function loadGroups()

	/**
	 * Loads the Groups that this user has access to
	 *
	 * @return AdminGroupWrapper the Admin Groups this user belongs to.
	 */
	protected function loadGroups()
	{
		$sql = sprintf('select AdminGroup.*
				from AdminGroup
				inner join AdminUserAdminGroupBinding on
					AdminUserAdminGroupBinding.groupnum = AdminGroup.id
				where usernum = %s',
				$this->db->quote($this->id, 'integer'));

		return SwatDB::query($this->db, $sql, 'AdminGroupWrapper');
	}

	// }}}
	// {{{ protected function getSerializablePrivateProperties()

	protected function getSerializablePrivateProperties()
	{
		$properties = parent::getSerializablePrivateProperties();
		$properties[] = 'google_2fa_authenticated';
		return $properties;
	}

	// }}}
}

?>
