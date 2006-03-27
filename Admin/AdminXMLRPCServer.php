<?php

require_once 'Swat/SwatXMLRPCServer.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';

/**
 * An XML-RPC server for admin applications
 *
 * This server will throw an AdminNotFoundException if no HTTP POST data is
 * provided with the page request.
 *
 * @package   Admin
 * @copyright 2006 silverorange
 */
class AdminXMLRPCServer extends SwatXMLRPCServer
{
	/**
	 * @xmlrpc.hidden
	 */
	public function init()
	{
		if (!isset($GLOBALS['HTTP_RAW_POST_DATA']))
			throw new AdminNotFoundException(Admin::_('Page not found.'));
	}
}

?>
