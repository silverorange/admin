<?php

/**
 * Web application module for sessions
 *
 * @package   Admin
 * @copyright 2005-2022 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminSessionModule extends SiteSessionModule
{


	/**
	 * @var array
	 * @see AdminSessionModule::registerLoginCallback()
	 */
	protected $login_callbacks = array();




	/**
	 * Creates a admin session module
	 *
	 * @param SiteApplication $app the application this module belongs to.
	 *
	 * @throws AdminException if there is no cookie module loaded the session
	 *                         module throws an exception.
	 *
	 * @throws AdminException if there is no database module loaded the session
	 *                         module throws an exception.
	 */
	public function __construct(SiteApplication $app)
	{
		$this->registerLoginCallback(
			array($this, 'regenerateAuthenticationToken'));

		parent::__construct($app);
	}




	public function init()
	{
		parent::init();

		// always activate the session for an admin
		if (!$this->isActive())
			$this->activate();

		if (!isset($this->user)) {
			$this->user = null;
			$this->history = array();
		} elseif ($this->user !== null) {
			$this->app->cookie->setCookie('email', $this->getEmailAddress(),
				strtotime('+1 day'), '/');
		}
	}




	/**
	 * Gets the module features this module depends on
	 *
	 * The admin session module depends on the SiteCookieModule and
	 * SiteDatabaseModule features.
	 *
	 * @return array an array of {@link SiteModuleDependency} objects defining
	 *                        the features this module depends on.
	 */
	public function depends()
	{
		$depends = parent::depends();
		$depends[] = new SiteApplicationModuleDependency('SiteCookieModule');
		$depends[] = new SiteApplicationModuleDependency('SiteCryptModule');
		$depends[] = new SiteApplicationModuleDependency('SiteDatabaseModule');
		return $depends;
	}




	/**
	 * Logs an admin user into an admin
	 *
	 * @param string $email
	 * @param string $password
	 *
	 * @return boolean true if the admin user was logged in is successfully and
	 *                  false if the admin user could not log in.
	 */
	public function login($email, $password)
	{
		$this->logout(); // make sure user is logged out before logging in

		$class_name = SwatDBClassMap::get('AdminUser');
		$user = new $class_name();
		$user->setDatabase($this->app->db);

		if ($user->loadFromEmail($email)) {
			$password_hash = $user->password;
			$password_salt = $user->password_salt;

			$crypt = $this->app->getModule('SiteCryptModule');

			if ($crypt->verifyHash($password, $password_hash, $password_salt)) {
				// No Crypt?! Crypt!
				if ($crypt->shouldUpdateHash($password_hash)) {
					$user->setPasswordHash($crypt->generateHash($password));
					$user->save();
				}

				$this->user = $user;
				$this->user->set2FaAuthenticated(false);

				if ($user->isAuthenticated($this->app)) {
					$this->insertUserHistory($user);
					$this->runLoginCallbacks();
				}
			}
		}

		return $this->isLoggedIn();
	}




	/**
	 * Logs the current admin user out of an admin
	 */
	public function logout()
	{
		$this->clear();
		$this->user = null;
	}




	/**
	 * Gets whether or not an admin user is logged in
	 *
	 * @return boolean true if an admin user is logged in and false if an
	 *                  admin user is not logged in.
	 */
	public function isLoggedIn()
	{
		return (isset($this->user) && $this->user !== null &&
			$this->user->isAuthenticated($this->app));
	}




	/**
	 * Gets the current admin user
	 *
	 * @return AdminUser the current admin user object, or null if an
	 *                   admin user is not logged in.
	 */
	public function getUser()
	{
		if (!$this->isLoggedIn())
			return null;

		return $this->user;
	}




	/**
	 * Gets the current admin user's user identifier
	 *
	 * @return string the current admin user's user identifier, or null if an
	 *                 admin user is not logged in.
	 */
	public function getUserID()
	{
		if (!$this->isLoggedIn())
			return null;

		return $this->user->id;
	}




	/**
	 * Gets the current admin user's email address
	 *
	 * @return string the current admin user's email address, or null if an
	 *                 admin user is not logged in.
	 */
	public function getEmailAddress()
	{
		if (!$this->isLoggedIn())
			return null;

		return $this->user->email;
	}




	/**
	 * Gets the current admin user's name
	 *
	 * @return string the current admin user's name, or null if an admin user
	 *                 is not logged in.
	 */
	public function getName()
	{
		if (!$this->isLoggedIn())
			return null;

		return $this->user->name;
	}




	/**
	 * loginWithTwoFactorAuthentication
	 *
	 * @param string $token
	 *
	 * @return boolean true if the admin user was logged in is successfully and
	 *                  false if the admin user could not log in.
	 */
	public function loginWithTwoFactorAuthentication($token)
	{
		$two_factor_authentication = new AdminTwoFactorAuthentication();
		$success = $two_factor_authentication->validateToken(
			$this->user->two_fa_secret,
			$token,
			$this->user->two_fa_timeslice
		);

		if ($success) {
			$this->user->save();
			$this->insertUserHistory($this->user);
			$this->runLoginCallbacks();
		}

		return $success;
	}




	/**
	 * Registers a callback function that is executed when a successful session
	 * login is performed
	 *
	 * @param callback $callback the callback to call when a successful login
	 *                            is performed.
	 * @param array $parameters optional. The paramaters to pass to the
	 *                           callback.
	 *
	 * @throws AdminException when the <i>$callback</i> parameter is not
	 *                        callable.
	 * @throws AdminException when the <i>$parameters</i> parameter is not an
	 *                        array.
	 */
	public function registerLoginCallback($callback, $parameters = array())
	{
		if (!is_callable($callback))
			throw new AdminException('Cannot register invalid callback.');

		if (!is_array($parameters))
			throw new AdminException('Callback parameters must be specified '.
				'in an array.');

		$this->login_callbacks[] = array(
			'callback' => $callback,
			'parameters' => $parameters
		);
	}




	protected function startSession()
	{
		parent::startSession();

		if (isset($this->user) && $this->user instanceof AdminUser) {
			$this->user->setDatabase($this->app->database->getConnection());
		}
	}




	/**
	 * Inserts login history for a user
	 *
	 * @param AdminUser $user_id the user to record login history for.
	 */
	protected function insertUserHistory(AdminUser $user)
	{
		$login_agent = (isset($_SERVER['HTTP_USER_AGENT'])) ?
			$_SERVER['HTTP_USER_AGENT'] : null;

		$remote_ip = $this->app->getRemoteIP();

		if (mb_strlen($login_agent) > 255) {
			$login_agent = mb_substr($login_agent, 0, 253).' …';
		}
		if (mb_strlen($remote_ip) > 15) {
			$remote_ip = mb_substr($remote_ip, 0, 13).' …';
		}

		$login_date = new SwatDate();
		$login_date->toUTC();

		$fields = array('integer:usernum','date:login_date',
			'text:login_agent', 'text:remote_ip', 'integer:instance');

		$values = array(
			'usernum'     => $user->id,
			'login_date'  => $login_date->getDate(),
			'login_agent' => $login_agent,
			'remote_ip'   => $remote_ip,
			'instance'    => $this->app->getInstanceId(),
		);

		SwatDB::insertRow($this->app->db, 'AdminUserHistory', $fields,
			$values);
	}




	protected function runLoginCallbacks()
	{
		foreach ($this->login_callbacks as $login_callback) {
			$callback = $login_callback['callback'];
			$parameters = $login_callback['parameters'];
			call_user_func_array($callback, $parameters);
		}
	}


}

?>
