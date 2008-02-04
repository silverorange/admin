<?php

/**
 * Stores the state of an admin menu-view
 *
 * The defaul menu view in the Admin package has collapsable menu sections and
 * a visibility toggle. This object stores the collspased/shown state of each
 * item in the menu-view.
 *
 * @package   Admin
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminMenuViewStateStore
{
	// {{{ public properties

	/**
	 * The identifier of this menu-view state store
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Whether or not the menu-view is shown
	 *
	 * @var boolean
	 */
	public $show;

	/**
	 * An array of boolean values containing the shown-state of each menu
	 * section
	 *
	 * The array is of the form:
	 * <code>
	 * array( $section_id => $show );
	 * </code>
	 *
	 * @var array
	 */
	public $sections_show = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new menu-view state store with the given id
	 *
	 * @param string $id the identifier of this menu-view state store.
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}

	// }}}
	// {{{ public function saveToSession()

	/**
	 * Saves this state to the user's session
	 *
	 * The session variable identifier used is this state store's identifier.
	 */
	public function saveToSession()
	{
		$serial_state = serialize($this);
		$_SESSION[$this->id] = $serial_state;
	}

	// }}}
	// {{{ public function saveToDatabase()

	/**
	 * Saves this state to a database
	 *
	 * @param MDB2_Driver_Common $db the database to which to save this menu
	 *                                state.
	 * @param integer $user_id the database identifier of the user for which
	 *                          this menu state is to be saved.
	 */
	public function saveToDatabase(MDB2_Driver_Common $db, $user_id)
	{
		$serial_state = serialize($this);
		$sql = sprintf('update AdminUser set menu_state = %s where id = %s',
			$db->quote($serial_state, 'text'),
			$db->quote($user_id, 'integer'));

		SwatDB::exec($db, $sql);
	}

	// }}}
	// {{{ public static function loadFromSession()

	/**
	 * Loads a menu-view state store from the user's session
	 *
	 * @param string $id the identifier of the state store to load from the
	 *                    user's session.
	 *
	 * @return AdminMenuViewStateStore the menu-view state store loaded from
	 *                                  the user's session or null if the
	 *                                  given state does not exist in the
	 *                                  user's session.
	 *
	 * @throws AdminException if the loaded state is not an instance of the
	 *                         AdminMenuViewStateStore class.
	 */
	public static function loadFromSession($id)
	{
		$state = null;

		if (isset($_SESSION[$id]) && is_string($_SESSION[$id])) {
			$state = unserialize($_SESSION[$id]);
			if (!($state instanceof self))
				throw new AdminException('Restored state is not a menu state '.
					'store');
		}

		return $state;
	}

	// }}}
	// {{{ public static function loadFromDatabase()

	/**
	 * Loads a menu-view state store from a database
	 *
	 * @param MDB2_Driver_Common $db the database from which to load the state.
	 * @param integer $user_id the database identifier of the user for which
	 *                          the menu state is to be loaded.
	 *
	 * @return AdminMenuViewStateStore the menu-view state store loaded from
	 *                                  the database or null if the database
	 *                                  does not contain a menu state for the
	 *                                  current user.
	 *
	 * @throws AdminException if the loaded state is not an instance of the
	 *                         AdminMenuViewStateStore class.
	 */
	public static function loadFromDatabase(MDB2_Driver_common $db, $user_id)
	{
		$state = null;

		$sql = sprintf('select menu_state from AdminUser where id = %s',
			$db->quote($user_id, 'integer'));

		$serial_state = SwatDB::queryOne($db, $sql);

		if ($serial_state !== null) {
			$state = unserialize($serial_state);
			if (!($state instanceof self))
				throw new AdminException('Restored state is not a menu state '.
					'store');
		}

		return $state;
	}

	// }}}
}

?>
