<?php

require_once 'Site/SiteMultipartMailMessage.php';
require_once 'Admin/dataobjects/AdminUser.php';
require_once 'Admin/exceptions/AdminException.php';

/**
 * Email that is sent to an admin uuser when their password has successfully
 * been reset
 *
 * @package   Admin
 * @copyright 2008 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       AdminUser::sendResetPasswordSuccessMailMessage()
 */
class AdminResetPasswordSuccessMailMessage extends SiteMultipartMailMessage
{
	// {{{ protected properties

	/**
	 * The user this reset password mail message is intended for
	 *
	 * @var AdminUser
	 */
	protected $admin_user;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new reset password email
	 *
	 * @param AdminApplication $app the application sending the mail message.
	 * @param AdminUser $user the user for which to create the email.
	 * @param string $password_link the URL of the application page that
	 *                               performs the password reset.
	 */
	public function __construct(AdminApplication $app, AdminUser $user,
		$password_link)
	{
		parent::__construct($app);
		$this->admin_user = $user;
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
				'send message. Make sure email is loaded on the user '.
				'object.');

		if ($this->admin_user->name === null)
			throw new AdminException('User requires a fullname to send '.
				'message. Make sure name is loaded on the user object.');

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
		return sprintf($this->getFormattedBody(
			"%s\n\n%%s%s\n%s\n\n%s\n%s"),
			$this->getTextInstanceNote());
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
		return sprintf($this->getFormattedBody(
			'<p>%s</p>%%s<p>%s<br />%s</p><p>%s<br />%s</p>'),
			$this->getHtmlInstanceNote());
	}

	// }}}
	// {{{ protected function getHtmlInstanceNote()

	protected function getHtmlInstanceNote()
	{
		$instance_note = '';

		if ($this->app->hasModule('SiteMultipleInstanceModule')) {
			$site_instance =
				$this->app->getModule('SiteMultipleInstanceModule');

			if (count($this->admin_user->instances) > 1) {
				$instance_note.= '<p>'.Admin::_(
					'Notice: Your admin password was also updated '.
					'for the following sites on which your admin account is '.
					'used:').
					'</p><p><ul>';

				foreach ($this->admin_user->instances as $instance) {
					if ($instance->id !== $site_instance->getId()) {
						$sql = sprintf('select value from InstanceConfigSetting
							where instance = %s and name = \'site.title\'',
							$this->app->db->quote($instance->id, 'integer'));

						$title = SwatDB::queryOne($this->app->db, $sql, 'text');
						$instance_note.=
							'<li>'.SwatString::minimizeEntities($title).'</li>';
					}
				}

				$instance_note.= '</ul></p>';
			}
		}

		return $instance_note;
	}

	// }}}
	// {{{ protected function getTextInstanceNote()

	protected function getTextInstanceNote()
	{
		$instance_note = '';

		if ($this->app->hasModule('SiteMultipleInstanceModule')) {
			$site_instance =
				$this->app->getModule('SiteMultipleInstanceModule');

			if (count($this->admin_user->instances) > 1) {
				$instance_note.= Admin::_(
					'Notice: Your admin password was also updated '.
					'for the following sites on which your admin account is '.
					'used:')."\n\n";

				foreach ($this->admin_user->instances as $instance) {
					if ($instance->id !== $site_instance->getId()) {
						$sql = sprintf('select value from InstanceConfigSetting
							where instance = %s and name = \'site.title\'',
							$this->app->db->quote($instance->id, 'integer'));

						$title = SwatDB::queryOne($this->app->db, $sql, 'text');
						$instance_note.= " - ".$title."\n";
					}
				}

				$instance_note.= "\n";
			}
		}

		return $instance_note;
	}

	// }}}
	// {{{ protected function getFormattedBody()

	protected function getFormattedBody($format_string)
	{

		return sprintf($format_string,
			sprintf(Admin::_('Your password for your %s account has '.
			'successfully been updated.'),
				$this->app->config->site->title),

			Admin::_('Why did I get this email?'),

			sprintf(Admin::_('This email confirms your password was '.
			'successfully updated after requesting a new password from %s.'),
				$this->app->config->site->title),

			Admin::_('I did not request a new password:'),

			sprintf(Admin::_('If you did not request a new password from %s '.
			'someone has gained access to your account. If this is the case '.
			'please contact your system administrator immediately.'),
				$this->app->config->site->title));
	}

	// }}}
}

?>
