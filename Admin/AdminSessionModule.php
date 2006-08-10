<?php

require_once 'Site/SiteSessionModule.php';
require_once 'SwatDB/SwatDB.php';
require_once 'Date.php';

/**
 * Web application module for sessions
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 */
class AdminSessionModule extends SiteSessionModule
{
    // {{{ public function init()

	public function init()
	{
		parent::init();

		// always activate the session for an admin
		if (!$this->isActive())
			$this->activate();

		if (!isset($this->user_id)) {
			$this->user_id =  0;
			$this->name =     '';
			$this->username = '';
			$this->history = array();
		} elseif ($this->user_id !== 0) {	
			setcookie($this->app->id.'_username', $this->username, 
				time() + 86400, '/', '', 0);
		}
	}

    // }}}
    // {{{ public function login()

	/**
	 * Logs an admin user into an admin
	 *
	 * @param string $username
	 * @param string $password
	 *
	 * @return boolean true if the admin user was logged in is successfully and
	 *                  false if the admin user could not log in.
	 */
	public function login($username, $password)
	{
		$logged_in = false;

		$this->logout(); //make sure user is logged out before logging in
	
		$md5_password = md5($password);
		
		$sql = 'select id, name, username from AdminUser
			where username = %s and password = %s and enabled = %s';

		$sql = sprintf($sql, 
			$this->app->db->quote($username, 'text'),
			$this->app->db->quote($md5_password, 'text'),
			$this->app->db->quote(true, 'boolean'));

		$row = SwatDB::queryRow($this->app->db, $sql);
		
		if ($row !== null) {
			$this->user_id  = $row->id;
			$this->name     = $row->name;
			$this->username = $row->username;

			$this->insertUserHistory($row->id);

			$logged_in = true;
		}

		return $logged_in;
	}

    // }}}
    // {{{ public function logout()

	/**
	 * Logs the current admin user out of an admin
	 */
	public function logout()
	{
		$_SESSION = array();
		$this->user_id = 0;
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
		return (isset($this->user_id) && $this->user_id !== 0);
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

		return $this->user_id;
	}

    // }}}
    // {{{ public function getUsername()

	/**
	 * Gets the current admin user's username
	 *
	 * @return string the current admin user's username, or null if an admin
	 *                 user is not logged in.
	 */
	public function getUsername()
	{
		if (!$this->isLoggedIn())
			return null;

		return $this->username;
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

		return $this->name;
	}

    // }}}
    // {{{ protected function insertUserHistory()

	/**
	 * Inserts login history for a user identifier
	 *
	 * @param integer $user_id the user identifier of the user to record
	 *                          login history for.
	 */
	protected function insertUserHistory($user_id)
	{
		$login_agent = (isset($_SERVER['HTTP_USER_AGENT'])) ? 
			$_SERVER['HTTP_USER_AGENT'] : null;

		$remote_ip = (isset($_SERVER['REMOTE_ADDR'])) ? 
			$_SERVER['REMOTE_ADDR'] : null;

		$login_date = new Date();
		$login_date->toUTC();

		$fields = array('integer:usernum','date:login_date',
			'text:login_agent', 'text:remote_ip');

		$values = array(
			'usernum'     => $user_id, 
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
