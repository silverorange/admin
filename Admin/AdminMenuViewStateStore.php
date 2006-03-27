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
 */
class AdminMenuViewStateStore
{
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

	/**
	 * Creates a new menu-view state store with the given id
	 *
	 * @param string $id the identifier of this menu-view state store.
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}

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

	/**
	 * Loads a menu-view state store from the user's session
	 *
	 * @param string $id the identifier of the state store to load from the
	 *                    user's session.
	 *
	 * @return AdminMenuViewStateStore the menu-view state store loaded from
	 *                                  the user's session of null if the
	 *                                  given state does not exist in the
	 *                                  user's session.
	 *
	 * @throws AdminException
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
}

?>
