<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';

/**
 * Very simple administrator logout page
 *
 * @package   Admin
 * @copyright 2005-2007 silverorange
 */
class AdminAdminSiteLogout extends AdminPage
{
	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		$form = $this->layout->logout_form;
		$form->process();

		if ($form->isProcessed()) {
			if (!$form->isAuthenticated()) {
				// add error message
				$message = new SwatMessage(Admin::_('Unable to log out.'),
					SwatMessage::WARNING);

				$message->secondary_content =
					Admin::_('In order to ensure your security, we were '.
					'unable to process your logout request. Please try again.');

				$this->app->messages->add($message);

				// go back where we came from
				$url = (isset($_SERVER['HTTP_REFERER'])) ?
					$_SERVER['HTTP_REFERER'] : 'Front';

				$this->app->relocate($url);
			} else {
				// log out
				$this->app->session->logout();
				$this->app->relocate($this->app->getBaseHref());
			}
		} else {
			throw new AdminNotFoundException();
		}
	}

	// }}}
}

?>
