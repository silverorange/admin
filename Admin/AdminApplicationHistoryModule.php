<?php

require_once 'Swat/SwatApplicationModule.php';

/**
 * Web application module for tracking page history
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminApplicationHistoryModule extends SwatApplicationModule
{
    // {{{ public function init()

	/**
	 * Initialize the module
	 */
	public function init()
	{
	}

    // }}}
    // {{{ public function storeHistory()

	public function storeHistory($url)
	{
		$history = &$_SESSION['history'];

		if (!is_array($history))
			$history = array();

		$has_querystring = strpos($url, '?');
	
		if (count($history) > 0) {
			end($history);
			$last = current($history);
			$pos = strpos($last, '?');

			if ($pos)
				$last = substr($last, 0, $pos);
		} else {
			$last = null;
		}

		$pos = strpos($url, '?');

		if ($pos)
			$base = substr($url, 0, $pos);
		else
			$base = $url;

		if ($has_querystring || strcmp($last, $base) != 0) {
			array_push($history, $url);
		}

		// throw away old ones
		while (count($history) > 10)
			array_shift($history);

	}

    // }}}
    // {{{ public function getHistory()

	public function getHistory($index = 1)
	{

		for ($i = 0; $i <= $index; $i++)
			$url = array_pop($_SESSION['history']);

		return $url;
	}

    // }}}
}

?>
