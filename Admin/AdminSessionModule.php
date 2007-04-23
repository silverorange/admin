<?php

require_once 'Admin/dataobjects/AdminUser.php';
require_once 'Admin/dataobjects/AdminUserWrapper.php';
require_once 'Admin/exceptions/AdminException.php';
require_once 'Site/SiteSessionModule.php';
require_once 'Site/SiteCookieModule.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Swat/SwatDate.php';

/**
 * Web application module for sessions
 *
 * @package   Admin
 * @copyright 2005-2007 silverorange
 */
class AdminSessionModule extends SiteSessionModule
{
	// {{{ public function __construct()

	/**
	 * Creates a admin session module
	 *
	 * @param SiteApplication $app the application this module belongs to.
	 *
	 * @throws AdminException if there is no cookie module loaded the session
	 *                         module throws an exception.
	 */
	public function __construct(SiteApplication $app)
	{
		if (!(isset($app->cookie) &&
			$app->cookie instanceof SiteCookieModule))
			throw new AdminException('The AdminSessionModule requires a '.
				'SiteCookieModule to be loaded. Please either explicitly '.
				'add a cookie module to the application before instantiating '.
				'the session module, or specify the cookie module before the '.
				'session module in the application\'s getDefaultModuleList() '.
				'method.');

		parent::__construct($app);
	}

	// }}}
    // {{{ public function init()

	public function init()
	{
		parent::init();

		// always activate the session for an admin
		if (!$this->isActive())
			$this->activate();

		if (!isset($this->user)) {
			$this->user = null;
			$this->force_change_password = false;
			$this->history = array();
		} elseif ($this->user !== null) {	
			$this->app->cookie->setCookie('email', $this->getEmailAddress(),
				strtotime('+1 day'), '/');
		}
	}

    // }}}
    // {{{ public function login()

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
	
		$md5_password = md5($password);
		
		$sql = sprintf('select *
			from AdminUser
			where email = %s and password = %s and enabled = %s',
			$this->app->db->quote($email, 'text'),
			$this->app->db->quote($md5_password, 'text'),
			$this->app->db->quote(true, 'boolean'));

		$user = SwatDB::query($this->app->db, $sql,
			'AdminUserWrapper')->getFirst();
		
		if ($user !== null) {
			$user->setDatabase($this->app->db);
			if ($user->force_change_password) {
				$this->force_change_password = true;
			} else {
				$this->user = $user;
				$this->insertUserHistory($user);
			}
		}

		return $this->isLoggedIn();
	}

    // }}}
    // {{{ public function logout()

	/**
	 * Logs the current admin user out of an admin
	 */
	public function logout()
	{
		$this->clear();
		$this->user = null;
		$this->force_change_password = false;
	}

    // }}}
    // {{{ public function isLoggedIn()

	/**
	 * Whether or not an admin user is logged in
	 *
	 * @return boolean true if an admin user is logged in and false if an
	 *                  admin user is not logged in.
	 */
	public function isLoggedIn()
	{
		return (isset($this->user) && $this->user !== null);
	}

    // }}}
    // {{{ public function getUserID()

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

    // }}}
    // {{{ public function getEmailAddress()

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

    // }}}
    // {{{ public function getName()

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

    // }}}
    // {{{ protected function insertUserHistory()

	/**
	 * Inserts login history for a user
	 *
	 * @param AdminUser $user_id the user to record login history for.
	 */
	protected function insertUserHistory(AdminUser $user)
	{
		$login_agent = (isset($_SERVER['HTTP_USER_AGENT'])) ? 
			$_SERVER['HTTP_USER_AGENT'] : null;

		$remote_ip = (isset($_SERVER['REMOTE_ADDR'])) ? 
			$_SERVER['REMOTE_ADDR'] : null;

		$login_date = new SwatDate();
		$login_date->toUTC();

		$fields = array('integer:usernum','date:login_date',
			'text:login_agent', 'text:remote_ip');

		$values = array(
			'usernum'     => $user->id, 
			'login_date'  => $login_date->getDate(), 
			'login_agent' => $login_agent, 
			'remote_ip'   => $remote_ip,
		);

		SwatDB::insertRow($this->app->db, 'AdminUserHistory', $fields,
			$values);
	}

    // }}}
}

?>
