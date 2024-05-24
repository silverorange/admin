<?php

/**
 * Page to reset the password for an admin user
 *
 * Users are required to enter a new password.
 *
 * @package   Admin
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       AdminUser
 */
class AdminAdminSiteResetPassword extends AdminPage
{
	// {{{ private properties

	/**
	 * @var string
	 */
	private $password_tag;

	/**
	 * @var AdminUser
	 */
	private $user;

	// }}}
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new AdminLoginLayout($this->app, AdminLoginTemplate::class);
	}

	// }}}

	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML(__DIR__.'/reset-password.xml');

		$this->password_tag = AdminApplication::initVar('password_tag');

		$this->initUser();

		$form = $this->ui->getWidget('edit_form');
		$form->addHiddenField('password_tag', $this->password_tag);
		$form->action = $this->source;

		$frame = $this->ui->getWidget('reset_password_frame');

		if ($this->user === null) {
			$frame->title = Admin::_('Update Password');
		} else {
			$frame->title = sprintf(Admin::_('Update Password for %s'),
				$this->user->name);
		}

		$confirm = $this->ui->getWidget('confirm_password');
		$confirm->password_widget = $this->ui->getWidget('password');
	}

	// }}}
	// {{{ protected function initUser()

	/**
	 * Initializes the admin user object associated with the password tag
	 */
	protected function initUser()
	{
		if ($this->password_tag !== null) {

			$sql = sprintf('select id from AdminUser
				where password_tag = %s and
					age(password_tag_date) < \'1 hour ago\'',
				$this->app->db->quote($this->password_tag, 'text'));

			$user_id = SwatDB::queryOne($this->app->db, $sql);
			if ($user_id !== null) {
				$class_name = SwatDBClassMap::get('AdminUser');
				$user = new $class_name();
				$user->setDatabase($this->app->db);
				if ($user->load($user_id)) {
					$this->user = $user;
				}
			}
		}
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		if ($this->user === null)
			return;

		$crypt = $this->app->getModule('SiteCryptModule');

		$form = $this->ui->getWidget('edit_form');

		if ($form->isProcessed()) {
			if (!$form->hasMessage()) {
				$password = $this->ui->getWidget('password')->value;

				$this->user->setPasswordHash($crypt->generateHash($password));
				$this->user->password_tag      = null;
				$this->user->password_tag_date = null;
				$this->user->save();

				$this->app->session->login($this->user->email, $password);

				$message = new SwatMessage(
					Admin::_('Your password has been updated.'));

				$this->app->messages->add($message);
				$this->sendResetPasswordSuccessMailMessage();
				$this->app->relocate($this->app->getBaseHref());
			}
		}
	}

	// }}}
	// {{{ protected function sendResetPasswordSuccessMailMessage()

	protected function sendResetPasswordSuccessMailMessage()
	{
		$mail_message = new AdminResetPasswordSuccessMailMessage($this->app,
			$this->user);

		$mail_message->smtp_server   = $this->app->config->email->smtp_server;
		$mail_message->smtp_port     = $this->app->config->email->smtp_port;
		$mail_message->smtp_username = $this->app->config->email->smtp_username;
		$mail_message->smtp_password = $this->app->config->email->smtp_password;

		$mail_message->from_address =
			$this->app->config->email->service_address;

		$mail_message->from_name = $this->app->config->site->title;

		$mail_message->subject = sprintf(
			Admin::_('Your %s Admin Password was Successfully Updated'),
			$this->app->config->site->title);

		$mail_message->send();
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildInternal()
	{
		parent::buildInternal();

		if ($this->user === null) {
			$text = sprintf(
				'<p>%s</p><ul><li>%s</li><li>%s</li><li>%s</li></ul>',
				Admin::_('Please verify that the link is exactly the same as '.
					'the one emailed to you.'),
				Admin::_('If you requested an email more than once, only the '.
					'most recent link will work.'),
				sprintf(Admin::_('The emailed link expires after one hour. '.
					'If the link has expired, %shave the email sent again%s.'),
					'<a href="AdminSite/ForgotPassword">', '</a>'),
				sprintf(Admin::_('If you have lost the link sent in the '.
					'email, you may %shave the email sent again%s.'),
					'<a href="AdminSite/ForgotPassword">', '</a>'));

			$message = new SwatMessage(Admin::_('Link Incorrect'), 'warning');
			$message->secondary_content = $text;
			$message->content_type = 'text/xml';
			$this->ui->getWidget('message_display')->add($message);

			$this->ui->getWidget('field_container')->visible = false;
		}
	}

	// }}}
}

?>
