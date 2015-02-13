<?php

require_once 'Swat/SwatContentBlock.php';
require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatFrame.php';
require_once 'Admin/layouts/AdminDefaultLayout.php';
require_once 'Site/pages/SiteXhtmlExceptionPage.php';

/**
 * Exception page in an admin application
 *
 * @package   Admin
 * @copyright 2006-2015 silverorange
 */
class AdminAdminSiteException extends SiteXhtmlExceptionPage
{
	// {{{ protected properties

	/**
	 * @var SwatContainer
	 */
	protected $container;

	// }}}
	// {{{ protected function createLayout()

	protected function createLayout()
	{
		return new AdminDefaultLayout($this->app,
			'Admin/layouts/xhtml/default.php');
	}

	// }}}

	// init phase
	// {{{ public function init()

	public function init()
	{
		parent::init();

		$this->container = new SwatFrame();
		$this->container->classes[] = 'admin-exception-container';
	}

	// }}}

	// build phase
	// {{{ public function build()

	public function build()
	{
		parent::build();
		if (isset($this->layout->navbar)) {
			$this->layout->navbar->popEntry();
			$this->layout->navbar->popEntry();
			$this->layout->navbar->createEntry('Error');
		}
	}

	// }}}
	// {{{ protected function display()

	protected function display()
	{
		ob_start();

		printf('<p>%s</p>', $this->getSummary());

		echo '<p>This error has been reported.</p>';

		if ($this->exception !== null) {
			$this->exception->process(false);
		}

		$content_block = new SwatContentBlock();
		$content_block->content = ob_get_clean();
		$content_block->content_type = 'text/xml';

		$this->container->add($content_block);
		$this->container->display();
	}

	// }}}

	// finalize phase
	// {{{ public function finalize()

	public function finalize()
	{
		parent::finalize();
		$this->layout->addHtmlHeadEntrySet(
			$this->container->getHtmlHeadEntrySet()
		);
	}

	// }}}
}

?>
