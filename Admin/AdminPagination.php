<?php

require_once 'Swat/SwatPagination.php';

/**
 * A widget to allow navigation between paginated data.
 * Preserves GET var information
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminPagination extends SwatPagination
{
	// {{{ public properties

	/**
	 * HTTP GET vars to clobber
	 *
	 * An array of GET variable names to unset before rebuilding a new link.
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
	 * This removes all unwanted elements from the get variables and adds
	 * all the wanted ones back into an acceptable url string.
	 *
	 * @return string the base link for all pages with cleaned get variables.
	 */
	protected function getLink()
	{
		//$vars = array_diff_key($_GET, array_flip($this->unset_get_vars));
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
