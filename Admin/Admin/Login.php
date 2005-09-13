<?php

require_once 'Admin/AdminPage.php';
require_once 'Admin/AdminUI.php';
require_once 'Swat/SwatMessage.php';
require_once 'Swat/SwatLayout.php';

/**
 * Administrator login page
 *
 * @package Admin
 * @copyright silverorange 2004
 */
class AdminLogin extends AdminPage
{
	public function process()
	{
		$form = $this->ui->getWidget('login_form');

		if ($form->process()) {
			if (!$form->hasMessage()) {
				$username = $this->ui->getWidget('username')->value;
				$password = $this->ui->getWidget('password')->value;
				$logged_in = $this->app->session->login($username, $password);
				
				if ($logged_in) {
					$this->app->relocate($this->app->getUri());
				} else {
					$frame = $this->ui->getWidget('login_frame');
					$msg = new SwatMessage(Admin::_('Login failed'), SwatMessage::ERROR);
					$frame->addMessage($msg);
				}
			}
		}
	}

	public function build()
	{
		ob_start();
		$this->ui->getRoot()->displayHtmlHeadEntries();
		$this->layout->html_head_entries = ob_get_clean();

		$this->layout->title = $this->app->title.' | '.$this->title;
		$this->layout->basehref = $this->app->getBaseHref();

		ob_start();
		$this->display();
		$this->layout->ui = ob_get_clean();

		ob_start();
		$this->displayJavascript();
		$this->layout->javascript = ob_get_clean();
	}

	protected function initInternal()
	{
		$this->ui->loadFromXML('Admin/Admin/login.xml');

		$frame = $this->ui->getWidget('login_frame');
		$frame->title = $this->app->title;

		$username = $this->ui->getWidget('username');

		if (isset($_COOKIE[$this->app->id.'_username']))
			$username->value = $_COOKIE[$this->app->id.'_username'];
		
		$form = $this->ui->getWidget('login_form');
		$form->action = $this->app->getUri();
	}

    protected function createLayout()
    {
        return new SwatLayout('Admin/layouts/login.php');
    }

	private function displayJavascript()
	{
		if (isset($_COOKIE[$this->app->id.'_username']))
			$username = $_COOKIE[$this->app->id.'_username'];
		else
			$username = '';
		
		echo '<script type="text/javascript" src="admin/javascript/admin-login.js"></script>'."\n";
		echo '<script type="text/javascript">';
		echo "\n adminLogin('username', 'password', '{$username}');";
		echo '</script>';
	}
}

?>
