<?php

/**
 * Administrator login page.
 *
 * @copyright 2005-2022 silverorange
 */
class AdminAdminSiteLogin extends AdminPage
{
    /**
     * Whether or not there was an error in the login information entered by
     * the user.
     *
     * @var bool
     */
    protected $login_error = false;

    protected function createLayout()
    {
        return new AdminLoginLayout($this->app, AdminLoginTemplate::class);
    }

    // init phase

    protected function initInternal()
    {
        $this->ui->loadFromXML(__DIR__ . '/login.xml');
        $this->ui->getWidget('login_form')->addJavaScript(
            'packages/admin/javascript/admin-login.js'
        );

        $frame = $this->ui->getWidget('login_frame');
        $frame->title = $this->app->title;

        $email = $this->ui->getWidget('email');

        try {
            if (isset($this->app->cookie->email)) {
                $email->value = $this->app->cookie->email;
            }
        } catch (SiteCookieException $e) {
            $this->app->cookie->removeCookie('email', '/');
        }

        $form = $this->ui->getWidget('login_form');
        $form->action = $this->app->getUri();

        if (!$this->app->config->admin->allow_reset_password) {
            $this->ui->getWidget('forgot_container')->visible = false;
        }
    }

    // process phase

    protected function processInternal()
    {
        parent::processInternal();

        $form = $this->ui->getWidget('login_form');

        if ($form->isProcessed() && !$form->hasMessage()) {
            $email = $this->ui->getWidget('email')->value;
            $password = $this->ui->getWidget('password')->value;
            $logged_in = $this->app->session->login($email, $password);

            if ($logged_in) {
                $this->app->relocate($this->app->getUri());
            } else {
                if (isset($this->app->session->user)
                    && $this->app->session->user->force_change_password) {
                    $this->app->replacePage('AdminSite/ChangePassword');
                } elseif (
                    isset($this->app->session->user)
                    && !$this->app->session->user->isActive()
                ) {
                    $message_display = $this->ui->getWidget('message_display');
                    $message = new SwatMessage(
                        Admin::_('Your account is inactive'),
                        'error'
                    );

                    $message->secondary_content = Admin::_(
                        'Please contact another administrator to reactivate ' .
                        'your account.'
                    );

                    $message_display->add($message);
                    $this->login_error = true;
                } elseif (isset($this->app->session->user)
                    && $this->app->is2FaEnabled()
                    && $this->app->session->user->two_fa_enabled
                    && !$this->app->session->user->is2FaAuthenticated()) {
                    $this->app->replacePage(
                        'AdminSite/TwoFactorAuthentication'
                    );
                } else {
                    $message_display = $this->ui->getWidget('message_display');
                    $message = new SwatMessage(
                        Admin::_('Login failed'),
                        'error'
                    );

                    $message->secondary_content =
                        Admin::_('Check your password and try again.');

                    $message_display->add($message);
                    $this->login_error = true;
                }
            }
        }
    }

    // build phase

    protected function display()
    {
        parent::display();
        $this->displayJavaScript();
    }

    private function displayJavaScript()
    {
        try {
            $email = $this->app->cookie->email ?? '';
        } catch (SiteCookieException $e) {
            $this->app->cookie->removeCookie('email', '/');
            $email = '';
        }

        $email = str_replace("'", "\\'", $email);
        $email = str_ireplace('</script>', "</script' + '>", $email);

        $login_error = ($this->login_error) ? 'true' : 'false';

        echo '<script type="text/javascript">';
        echo "\nAdminLogin('email', 'password', 'login_button', " .
            "'{$email}', {$login_error});";

        echo '</script>';
    }

    // finalize phase

    public function finalize()
    {
        parent::finalize();

        $this->layout->addHtmlHeadEntry(
            'packages/admin/styles/admin-login-page.css'
        );
    }
}
