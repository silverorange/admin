<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/layouts/AdminLoginLayout.php';
require_once 'Admin/dataobjects/AdminUser.php';
require_once 'Swat/SwatMessage.php';

/**
 * Administrator forgot password page
 *
 * @package   Admin
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       AdminUser
 */
class AdminAdminSiteForgotPassword extends AdminPage
{
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
		$this->ui->loadFromXML(dirname(__FILE__).'/forgot-password.xml');

		$frame = $this->ui->getWidget('forgot_password_frame');
		$frame->title = $this->app->title;

		$email = $this->ui->getWidget('email');
		try {
			if (isset($this->app->cookie->email))
				$email->value = $this->app->cookie->email;

		} catch (SiteCookieException $e) {
			$this->app->cookie->removeCookie('email', '/');
		}

		$form = $this->ui->getWidget('forgot_password_form');
		$form->action = $this->app->getUri();
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		$form = $this->ui->getWidget('forgot_password_form');

		if ($form->isProcessed() && !$form->hasMessage()) {
			$this->generatePasswordLink();
		}
	}

	// }}}
	// {{{ protected function generatePasswordLink()

	protected function generatePasswordLink()
	{
		$email = $this->ui->getWidget('email')->value;

		$admin_user = $this->getAccount($email);

		if ($admin_user === null) {
			$message = new SwatMessage(Admin::_(
				'There is no account with this email address.'),
				SwatMessage::ERROR);

			$message->secondary_content = Admin::_(
				'Make sure you entered the email correctly');

			$message->content_type = 'text/xml';
			$this->ui->getWidget('email')->addMessage($message);
		} else {
			$this->generateResetPasswordEmail($admin_user);
			$message = new SwatMessage(Admin::_(
				'Reset Password Email Sent'),
				SwatMessage::NOTIFICATION);

			$message->secondary_content = sprintf(Admin::_(
				'An email has been sent to "%s" with a link to create your '.
				'new password'), $email);

			$message_display = $this->ui->getWidget('message_display');
			$message_display->add($message, SwatMessageDisplay::DISMISS_OFF);

			$container = $this->ui->getWidget('field_container');
			$container->visible = false;
		}
	}

	// }}}
	// {{{ protected function getAccount()

	/**
	 * Gets the user to which to send the forgot password email
	 *
	 * @param string $email the email address of the admin user.
	 *
	 * @return AdminUser the user or null if no such user exists.
	 */
	protected function getAccount($email)
	{
		$class_name = SwatDBClassMap::get('AdminUser');
		$admin_user = new $class_name();
		$admin_user->setDatabase($this->app->db);
		$found = $admin_user->loadFromEmail($email, $instance);

		if ($found === false)
			$admin_user = null;

		return $admin_user;
	}

	// }}}
	// {{{ protected function generateResetPasswordEmail()

	protected function generateResetPasswordEmail(AdminUser $admin_user)
	{
		$password_tag = $admin_user->resetPassword($this->app);
		$password_link = $this->app->getBaseHref().
			'AdminSite/ResetPassword?password_tag='.$password_tag;

		$admin_user->sendResetPasswordMailMessage($this->app, $password_link);
	}

	// }}}
}

?>
