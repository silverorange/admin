<?php

require_once 'Swat/SwatPagination.php';

/**
 * A pagination widget that preserves HTTP GET variables
 *
 * @package   Admin
 * @copyright 2004-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminPagination extends SwatPagination
{
	// {{{ public properties

	/**
	 * HTTP GET varables that are not to be preserved
	 *
	 * @var array
	 */
	public $unset_get_vars = array();

	// }}}
	// {{{ public function process()

	/**
	 * Processes this pagination widget
	 *
	 * Sets the current_page and current_record properties.
	 */
	public function process()
	{
		parent::process();

		if (array_key_exists($this->id, $_GET))
			$this->current_page = $_GET[$this->id];

		$this->current_record = $this->current_page * $this->page_size;
	}

	// }}}
	// {{{ protected function getLink()

	/**
	 * Gets the base link for all page links
	 *
	 * This removes all unwanted variables from the current HTTP GET variables
	 * and adds all wanted variables ones back into the link string.
	 *
	 * @return string the base link for all pages with cleaned HTTP GET
	 *                 variables.
	 */
	protected function getLink()
	{
		$vars = $_GET;

		$this->unset_get_vars[] = $this->id;
		$this->unset_get_vars[] = 'source';

		foreach($vars as $name => $value)
			if (in_array($name, $this->unset_get_vars))
				unset($vars[$name]);

		if ($this->link === null)
			$link = '?';
		else
			$link = $this->link.'?';

		foreach($vars as $name => $value)
			$link .= $name.'='.urlencode($value).'&';

		$link.= urlencode($this->id).'=%s';

		return $link;
	}

	// }}}
}

?>
