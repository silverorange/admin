<?php

require_once 'Site/SiteApplicationModule.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Date.php';

/**
 * Web application module for sessions
 *
 * @package Admin
 * @copyright 2004-2006 silverorange
 */
class AdminSessionModule extends SiteApplicationModule
{
    // {{{ public function init()

	public function init()
	{
		session_cache_limiter('');
		session_save_path('/so/phpsessions/'.$this->app->id);
		session_name($this->app->id);
		session_start();

		if (!isset($_SESSION['user_id'])) {
			$_SESSION['user_id'] = 0;
			$_SESSION['name'] = '';
			$_SESSION['username'] = '';
			$_SESSION['history'] = array();
		} elseif ($_SESSION['user_id'] != 0) {	
			setcookie($this->app->id.'_username', $_SESSION['username'], 
				time() + 86400, '/', '', 0);
		}
	}

    // }}}
    // {{{ public function login()

	/**
	 * Authenticate user
	 * @param string $username
	 * @param string $password
	 * @return bool True if login is successful.
	 */
	public function login($username, $password)
	{
		$this->logout(); //make sure user is logged out before logging in
	
		$md5_password = md5($password);
		
		$sql = "select id, name, username from AdminUser
				where username = %s and password = %s and enabled = %s";

		$sql = sprintf($sql, 
			$this->app->db->quote($username, 'text'),
			$this->app->db->quote($md5_password, 'text'),
			$this->app->db->quote(true, 'boolean'));

		$row = SwatDB::queryRow($this->app->db, $sql);
		
		if ($row !== null) {
			$_SESSION['user_id'] = $row->id;
			$_SESSION['name']   = $row->name;
			$_SESSION['username']   = $row->username;

			$this->insertUserHistory($row->id);

			return true;
		} else {
			return false;
		}
	}

    // }}}
    // {{{ private function insertUserHistory()

	private function insertUserHistory($userid)
	{
		$user_agent = (isset($_SERVER['HTTP_USER_AGENT'])) ? 
			$_SERVER['HTTP_USER_AGENT'] : null;
		$remote_ip = (isset($_SERVER['REMOTE_ADDR'])) ? 
			$_SERVER['REMOTE_ADDR'] : null;
		$login_date = new Date();
		$login_date->toUTC();

		$fields = array('integer:usernum','date:login_date', 'login_agent', 
			'remote_ip');
		$values = array('usernum' => $userid, 
			'logindate' => $login_date->getDate(), 'loginagent' => $user_agent, 
			'remoteip' => $remote_ip);
		SwatDB::insertRow($this->app->db, 'AdminUserHistory', $fields, $values);
	}

    // }}}
    // {{{ public function logout()

	/**
	 * Set the user as logged-out 
	 */
	public function logout()
	{
		$_SESSION = array();
		$_SESSION['user_id'] = 0;
	}

    // }}}
    // {{{ public function isLoggedIn()

	/**
	 * Check the user's logged-in status
	 * @return bool True if user is logged in. 
	 */
	public function isLoggedIn()
	{
		if (isset($_SESSION['user_id']))
			return ($_SESSION['user_id'] != 0);

		return false;
	}

    // }}}
    // {{{ public function getUserID()

	/**
	 * Retrieve the current user ID
	 * @return integer current user ID, or null if not logged in.
	 */
	public function getUserID()
	{
		if (!$this->isLoggedIn())
			return null;

		return $_SESSION['user_id'];
	}

    // }}}
    // {{{ public function getUsername()

	/**
	 * Retrieve the current user username
	 * @return varchar current username, or null if not logged in.
	 */
	public function getUsername()
	{
		if (!$this->isLoggedIn())
			return null;

		return $_SESSION['username'];
	}

    // }}}
    // {{{ public function getName()

	/**
	 * Retrieve the current user name
	 * @return varchar current name, or null if not logged in.
	 */
	public function getName()
	{
		if (!$this->isLoggedIn())
			return null;

		return $_SESSION['name'];
	}

    // }}}
}

?>
