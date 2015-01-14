<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/layouts/AdminLoginLayout.php';
require_once 'Admin/dataobjects/AdminUser.php';
require_once 'Swat/SwatMessage.php';

/**
 * Administrator forgot password page
 *
 * @package   Admin
 * @copyright 2007-2015 silverorange
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
		$this->ui->loadFromXML(__DIR__.'/forgot-password.xml');

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
				'There is no account with the specified email address'),
				'error');

			$message->secondary_content = Admin::_(
				'Make sure you entered the email address correctly.');

			$message->content_type = 'text/xml';
			$this->ui->getWidget('email')->addMessage($message);
		} else {
			try {
				$this->sendResetPasswordMailMessage($admin_user);

				$primary_text = Admin::_('Email has been sent');
				$anchor_tag = new SwatHtmlTag('a');
				$anchor_tag->href = 'mailto:'.$email;
				$anchor_tag->setContent($email);

				/*
				 * Don't show other site instances here as it could violate the
				 * user's privacy. Another user is resetting the password and may
				 * have no knowledge of other instances the user belongs to.
				 */
				$strong_tag = new SwatHtmlTag('strong');
				$strong_tag->setContent(sprintf(
					Admin::_('Reset Your %s Admin Password'),
					$this->app->config->site->title));

				$secondary_text = sprintf(Admin::_(
					'%sAn email has been sent to %s containing a link to '.
					'create a new password for the %s admin.%s%sPlease check  '.
					'your mail for a new message with the subject: %s.%s'),
					'<p>', $anchor_tag, $this->app->config->site->title,
					'</p>', '<p>', $strong_tag, '</p>');

				$message_type = 'notice';
			} catch (SiteException $exception) {
				$exception->process(false);

				$primary_text = Admin::_('Unable to send email');
				$secondary_text = sprintf(Admin::_('%1$s
					%3$sThis problem has been reported.%4$s
					%3$sPlease contact the site administrator if this problem
						continues.%4$s
					%2$s'),
					'<ul>', '</ul>', '<li>', '</li>');

				$message_type = 'error';
			}

			$message = new SwatMessage($primary_text, $message_type);
			$message->content_type = 'text/xml';
			$message->secondary_content = $secondary_text;

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
		$found = $admin_user->loadFromEmail($email);

		if ($found === false)
			$admin_user = null;

		return $admin_user;
	}

	// }}}
	// {{{ protected function sendResetPasswordMailMessage()

	protected function sendResetPasswordMailMessage(AdminUser $admin_user)
	{
		$password_tag = $admin_user->resetPassword();
		$password_link = $this->app->getBaseHref().
			'AdminSite/ResetPassword?password_tag='.$password_tag;

		$mail_message = new AdminResetPasswordMailMessage($this->app,
			$admin_user, $password_link);

		$mail_message->smtp_server = $this->app->config->email->smtp_server;
		$mail_message->from_address =
			$this->app->config->email->service_address;

		$mail_message->from_name = $this->app->config->site->title;

		$mail_message->subject = sprintf(
			Admin::_('Reset Your %s Admin Password'),
			$this->app->config->site->title);

		$mail_message->send();
	}

	// }}}
}

?>
