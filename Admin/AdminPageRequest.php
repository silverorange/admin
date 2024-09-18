<?php

/**
 * Page request
 *
 * @package   Admin
 * @copyright 2004-2022 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminPageRequest extends SiteObject
{


	protected $source;
	protected $component;
	protected $subcomponent;
	protected $title;
	protected $app;
	protected $class_prefix;



	/**
	 * Creates a new page request and resolves the component for the request
	 *
	 * @param AdminApplication $app the admin application creating the page
	 *                               request.
	 * @param string $source the source of the page request.
	 */
	public function __construct(AdminApplication $app, $source)
	{
		$this->source = $source;
		$this->app = $app;

		if ($this->source == '')
			$this->source = $this->app->getFrontSource();

		$allow_reset_password =
			(boolean)$app->config->admin->allow_reset_password;

		if ($this->app->session->isLoggedIn()) {
			$source_exp = explode('/', $this->source);

			if (count($source_exp) == 1) {
				$this->component = $this->source;
				$this->subcomponent = $this->app->getDefaultSubComponent();
			} elseif (count($source_exp) == 2) {
				[$this->component, $this->subcomponent] = $source_exp;
			} else {
				throw new AdminNotFoundException(sprintf(Admin::_(
					"Invalid source '%s'."),
					$this->source));
			}

			if ($this->component == 'AdminSite') {
				$admin_titles = $this->getAdminSiteTitles();

				if (isset($admin_titles[$this->subcomponent])) {
					$this->title = $admin_titles[$this->subcomponent];
				} else {
					throw new AdminNotFoundException(
						sprintf(
							Admin::_("Component not found for source '%s'."),
							$this->source
						)
					);
				}

			} else {
				$component = new AdminComponent();
				$component->setDatabase($this->app->db);
				if (!$component->loadFromShortname($this->component)) {
					throw new AdminNotFoundException(sprintf(Admin::_(
						"Component not found for source '%s'."),
						$this->source));
				} else {
					$this->title = $component->title;
				}

				if (!$this->app->session->user->hasAccess($component)) {
					$user = $this->app->session->user;
					throw new AdminNoAccessException(sprintf(Admin::_(
						"Access to the requested component is forbidden for ".
						"user '%s'."), $user->id), 0, $user);
				}
			}
		} else {
			switch ($this->source) {
			case 'AdminSite/ChangePassword':
				$this->subcomponent = 'ChangePassword';
				break;

			case 'AdminSite/TwoFactorAuthentication':
				$this->subcomponent = 'TwoFactorAuthentication';
				break;

			case 'AdminSite/ForgotPassword':
				if (!$allow_reset_password) {
					throw new AdminNotFoundException(sprintf(Admin::_(
						"Component not found for source '%s'."),
						$this->source));
				}

				$this->subcomponent = 'ForgotPassword';
				break;

			case 'AdminSite/ResetPassword':
				$this->subcomponent = 'ResetPassword';
				break;

			default:
				$this->subcomponent = 'Login';
				break;
			}

			$admin_titles = $this->getAdminSiteTitles();
			$this->title = $admin_titles[$this->subcomponent];
			$this->component = 'AdminSite';
		}
	}



	public function getClassName()
	{
		$class_name = null;

		$prefixes = array_keys($this->app->getComponentIncludePaths());

		// Try non-prefixed class first.
		array_unshift($prefixes, null);

		foreach ($prefixes as $prefix) {
			$class_name = ($prefix === null)
				? $this->component.$this->subcomponent
				: $prefix.$this->component.$this->subcomponent;

			if (class_exists($class_name)) {
				break;
			}
		}

		return $class_name;
	}



	public function getTitle()
	{
		return $this->title;
	}



	public function getComponent()
	{
		return $this->component;
	}



	public function getSubComponent()
	{
		return $this->subcomponent;
	}



	public function getSource()
	{
		return $this->source;
	}



	public function getAdminSiteTitles()
	{
		return ['Profile'                 => Admin::_('Edit User Profile'), 'Logout'                  => Admin::_('Logout'), 'Login'                   => Admin::_('Login'), 'Exception'               => Admin::_('Exception'), 'Front'                   => Admin::_('Index'), 'ChangePassword'          => Admin::_('Change Password'), 'ResetPassword'           => Admin::_('Update Password'), 'ForgotPassword'          => Admin::_('Reset Forgotten Password'), 'TwoFactorAuthentication' => Admin::_('Two Factor Authentication'), 'MenuViewServer'          => ''];
	}

}

?>
