<?php

require_once 'Swat/SwatUI.php';
require_once 'Swat/SwatMessage.php';
require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/layouts/AdminLoginLayout.php';

/**
 * Page to reset the password for an admin user
 *
 * Users are required to enter a new password.
 *
 * @package   Admin
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       AdminUser
 */
class AdminAdminSiteResetPassword extends AdminPage
{
	// {{{ private properties

	private $password_tag = null;
	private $user_id;
	private $user;

	// }}}
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new AdminLoginLayout($this->app,
			'Admin/layouts/xhtml/login.php');
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML(dirname(__FILE__).'/reset-password.xml');

		$this->password_tag = AdminApplication::initVar('password_tag', null,
			AdminApplication::VAR_GET);

		if ($this->password_tag === null)
			$this->user_id = null;
		else
			$this->user_id = $this->getUserId($this->password_tag);

		$frame = $this->ui->getWidget('reset_password_frame');
		$frame->title = $this->app->title;

		$form = $this->ui->getWidget('edit_form');
		$form->action = $this->source.'?password_tag='.$this->password_tag;

		$confirm = $this->ui->getWidget('confirm_password');
		$confirm->password_widget = $this->ui->getWidget('password');;
	}

	// }}}
	// {{{ protected function getUserId()

	/**
	 * Gets the account id of the account associated with the password tag
	 *
	 * @param string $password_tag the password tag.
	 *
	 * @return integer the account id of the account associated with the
	 *                  password tag or null if no such account id exists.
	 */
	protected function getUserId($password_tag)
	{
		$sql = sprintf('select id from AdminUser where password_tag = %s',
			$this->app->db->quote($password_tag, 'text'));

		return SwatDB::queryOne($this->app->db, $sql);
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		if ($this->user_id === null)
			return;

		$form = $this->ui->getWidget('edit_form');

		if ($form->isProcessed()) {
			if (!$form->hasMessage()) {
				$password = $this->ui->getWidget('password')->value;

				$this->user = new AdminUser();
				$this->user->setDatabase($this->app->db);
				$this->user->load($this->user_id);
				$this->user->setPassword($password);
				$this->user->password_tag = null;
				$this->user->save();

				$logged_in = $this->app->session->login($this->user->email,
					$password);

				$message = new SwatMessage(
					Admin::_('Your password has been updated.'));

				$this->app->messages->add($message);
				$this->app->relocate($this->app->getBaseHref());
			}
		}
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		if ($this->user_id === null) {
			$text = sprintf('<p>%s</p><ul><li>%s</li><li>%s</li></ul>',
				Admin::_('Please verify that the link is exactly the same as '.
					'the one emailed to you.'),
				Admin::_('If you requested an email more than once, only the '.
					'most recent link will work.'),
				sprintf(Site::_('If you have lost the link sent in the '.
					'email, you may %shave the email sent again%s.'),
					'<a href="AdminSite/ForgotPassword">', '</a>'));

			$message = new SwatMessage(Admin::_('Link Incorrect'),
				SwatMessage::WARNING);

			$message->secondary_content = $text;
			$message->content_type = 'text/xml';
			$this->ui->getWidget('message_display')->add($message);

			$this->ui->getWidget('field_container')->visible = false;
		}
	}

	// }}}
}

?>
