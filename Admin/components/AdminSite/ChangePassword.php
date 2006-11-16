<?php

require_once 'SwatDB/SwatDB.php';
require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/layouts/AdminLayout.php';
require_once 'Admin/AdminUI.php';
require_once 'Swat/SwatMessage.php';

/**
 * Force change password page after initial login
 *
 * @package   Admin
 * @copyright 2005-2006 silverorange
 */
class AdminAdminSiteChangePassword extends AdminPage
{
	// {{{ public properties

	public $email;

	// }}}

	// init phase
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new SiteLayout($this->app, 'Admin/layouts/xhtml/change-password.php');
	}

	// }}}
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		parent::initInternal();

		$this->ui->loadFromXML(dirname(__FILE__).'/change-password.xml');

		$confirm = $this->ui->getWidget('confirm_password');
		$confirm->password_widget = $this->ui->getWidget('password');

		$form = $this->ui->getWidget('change_password_form');
		$form->action = 'AdminSite/ChangePassword';
	}

	// }}}
	// {{{ public function setEmail()

	/**
	 * Set items 
	 *
	 * @param string $email The email of the user logging in
	 */
	public function setEmail($email)
	{
		$form = $this->ui->getWidget('change_password_form');
		$form->addHiddenField('email', $email);
		$form->addHiddenField('relocate_uri', $this->app->getUri());
	}

	// }}}

	// process phase
	// {{{ protected function processInternal()

	protected function processInternal()
	{
		parent::processInternal();

		$form = $this->ui->getWidget('change_password_form');
		$email = $form->getHiddenField('email');

		$this->validate($email);

		if ($form->isProcessed() && !$form->hasMessage()) {
			$old_password = $this->ui->getWidget('old_password')->value;

			$password = $this->ui->getWidget('password')->value;

			$fields = array('text:password', 'boolean:force_change_password');
			$values = array('password' => md5($password),
				'force_change_password' => false);

			SwatDB::updateRow($this->app->db, 'AdminUser', $fields, $values,
				'text:email', $email);

			$this->app->session->login($email, $password);

			$msg = new SwatMessage(Admin::_('Your password has been updated.'));
			$this->app->messages->add($msg);

			$uri = $form->getHiddenField('relocate_uri');
			$this->app->relocate($uri);
		}
	}

	// }}}
	// {{{ protected function validate()

	protected function validate($email)
	{
		$old_password = $this->ui->getWidget('old_password')->value;
		$new_password = $this->ui->getWidget('password')->value;

		if ($old_password === null || $new_password === null)
			return;

		if ($old_password == $new_password) {
			$msg = new SwatMessage(
				Admin::_('Your new password can not
					be the same as your old password'),
				SwatMessage::ERROR);

			$this->ui->getWidget('password')->addMessage($msg);
		}

		$sql = sprintf('select id from AdminUser
			where email = %s and password = %s',
			$this->app->db->quote($email, 'text'),
			$this->app->db->quote(md5($old_password), 'text'));

		$id = SwatDB::queryOne($this->app->db, $sql);

		if ($id === null) {
			$msg = new SwatMessage(
				Admin::_('Your old password is not correct'),
				SwatMessage::ERROR);

			$this->ui->getWidget('old_password')->addMessage($msg);
		}
	}

	// }}}
}

?>
