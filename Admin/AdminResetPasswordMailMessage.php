<?php

require_once 'Site/SiteMultipartMailMessage.php';
require_once 'Site/dataobjects/SiteAccount.php';
require_once 'Admin/exceptions/AdminException.php';

/**
 * Email that is sent to user's holder when they request new passwords
 *
 * To send a password reset message:
 * <code>
 * $password_link = '/account/resetpassword'
 * $email = new AdminResetPasswordMailMessage($app, $account, $password_link,
 *     'My Application Title');
 *
 * $email->smtp_server = 'example.com';
 * $email->from_address = 'service@example.com';
 * $email->from_name = 'Customer Service';
 * $email->subject = 'Reset Your Password';
 *
 * $email->send();
 * </code>
 *
 * @package   Admin
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       AdminUser
 */
class AdminResetPasswordMailMessage extends SiteMultipartMailMessage
{
	// {{{ protected properties

	/**
	 * The user this reset password mail message is intended for
	 *
	 * @var AdminUser
	 */
	protected $admin_user;

	/**
	 * The URL of the application page that performs that password reset
	 * action
	 *
	 * @var string
	 */
	protected $password_link;

	/**
	 * The title of the application sending the reset password mail
	 *
	 * This title is visible inside the mail message bodytext.
	 *
	 * @var string
	 */
	protected $application_title;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new reset password email
	 *
	 * @param SiteAccount $account the account to create the email for.
	 * @param string $password_link the URL of the application page that
	 *                               performs the password reset.
	 */
	public function __construct(AdminApplication $app, AdminUser $user,
		$password_link, $application_title)
	{
		parent::__construct($app);

		$this->password_link = $password_link;
		$this->admin_user = $user;
		$this->application_title = $application_title;
	}

	// }}}
	// {{{ public function send()

	/**
	 * Sends this mail message
	 */
	public function send()
	{
		if ($this->admin_user->email === null)
			throw new AdminException('User requires an email address to '.
				'reset password. Make sure email is loaded on the user '.
				'object.');

		if ($this->admin_user->name === null)
			throw new AdminException('User requires a fullname to reset '.
				'password. Make sure name is loaded on the user object.');

		$this->to_address = $this->admin_user->email;
		$this->to_name = $this->admin_user->name;
		$this->text_body = $this->getTextBody();
		$this->html_body = $this->getHtmlBody();

		parent::send();
	}

	// }}}
	// {{{ protected function getTextBody()

	/**
	 * Gets the plain-text content of this mail message
	 *
	 * @return string the plain-text content of this mail message.
	 */
	protected function getTextBody()
	{
		return $this->getFormattedBody(
			"%s\n\n%s\n\n%s\n\n%s\n%s\n\n%s\n%s",
			$this->password_link);
	}

	// }}}
	// {{{ protected function getHtmlBody()

	/**
	 * Gets the HTML content of this mail message
	 *
	 * @return string the HTML content of this mail message.
	 */
	protected function getHtmlBody()
	{
		return $this->getFormattedBody(
			'<p>%s</p><p>%s</p><p>%s</p><p>%s<br />%s</p><p>%s<br />%s</p>',
			sprintf('<a href="%1$s">%1$s</a>', $this->password_link));
	}

	// }}}
	// {{{ protected function getFormattedBody()

	protected function getFormattedBody($format_string, $formatted_link)
	{
		return sprintf($format_string,
			sprintf(Admin::_('This email is in response to your recent '.
			'request for a new password for your %s account. Your password '.
			'has not yet been changed. Please click on the following link '.
			'and follow the steps to change your account password.'),
				$this->application_title),

			$formatted_link,

			Admin::_('Clicking on this link will take you to a page that '.
			'requires you to enter in and confirm a new password. Once you '.
			'have chosen and confirmed your new password you will be taken to '.
			'your account page.'),

			Admin::_('Why did I get this email?'),

			Admin::_('When someone forgets their password the best way '.
			'for us to verify their identity is to send an email to the '.
			'address listed in their account. By clicking on the link above '.
			'you are verifying that you requested a new password for your '.
			'account.'),

			Admin::_('I did not request a new password:'),

			sprintf(Admin::_('If you did not request a new password from %s '.
			'then someone may have accidentally entered your email when '.
			'requesting a new password. Have no fear! Your account '.
			'information is safe. Simply ignore this email and continue '.
			'using your existing password.'),
				$this->application_title));
	}

	// }}}
}

?>
