<?php

require_once 'Admin/pages/AdminPage.php';
require_once 'Admin/exceptions/AdminNotFoundException.php';
require_once 'Swat/SwatDate.php';

/**
 * Page for Webalizer component
 *
 * @package   Admin
 * @copyright 2006 silverorange
 */
class WebalizerIndex extends AdminPage
{
	public $webalizer_root = '';

	private $id;
	
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/webalizer.xml');
		$this->ui->getRoot()->addStyleSheet('admin/styles/webalizer.css');

		$this->id = SwatApplication::initVar('id', null, SwatApplication::VAR_GET);
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildinternal()
	{
		parent::buildinternal();

		$this->buildNavBar();

		$stats_content = $this->ui->getWidget('stats_content');

		ob_start();

		if ($this->id === null)
			$this->displayIndex();
		else
			$this->displayStats();

		$stats_content->content = ob_get_clean();
	}

	// }}}
	// {{{ private function displayStats()

	protected function displayStats()
	{
		echo '<div id="webalizer-content">';

		$filename = $this->webalizer_root.$this->id.'.html';

		if (file_exists($filename))
			$this->displayFile($filename);
		else
			throw new AdminNotFoundException("Unable to find stats file '$filename'.");

		echo '</div>';
	}

	// }}}
	// {{{ private function displayIndex()

	protected function displayIndex()
	{
		$links = $this->findIndexFiles();
		arsort($links);
		echo '<ul>';

		foreach ($links as $link) {
			$id = substr($link, 0, 10);
			$year = substr($link, 6, 4);
			$anchor = new SwatHtmlTag('a');
			$anchor->href = $this->source.'?id='.$id;
			$anchor->setContent($year);

			echo '<li>';
			$anchor->display();
			echo '</li>';
		}

		echo '</ul>';
	}

	// }}}
	// {{{ private function findIndexFiles()

	private function findIndexFiles()
	{
		$matches = array();
		$expression = '/^index_[0-9]{4}\.html$/i';

		foreach (scandir($this->webalizer_root) as $filename)
			if (preg_match($expression, $filename))
				$matches[] = $filename;

		return $matches;
	}

	// }}}
	// {{{ private function displayFile()

	private function displayFile($filename = '/tmp/index.html')
	{
		$lines = file($filename);

		foreach ($lines as $line) {
			$line = eregi_replace('(usage_.*)\.html',
				$this->source.'?id=\\1', $line);

			$line = eregi_replace('img src="',
				'img src="webalizer/images/', $line);

			echo $line;
		}
	}

	// }}}
	// {{{ protected function buildNavBar()

	protected function buildNavBar()
	{
		if ($this->id === null)
			return;

		$component_entry = $this->navbar->popEntry();
		$component_entry->link = $this->source;
		$this->navbar->addEntry($component_entry);

		if (strncmp($this->id, 'index', 5) == 0) {
			$year = substr($this->id, 6, 4);
			$this->navbar->createEntry($year);
		}

		if (strncmp($this->id, 'usage', 5) == 0) {
			$date = new SwatDate();
			$year = substr($this->id, 6, 4);
			$month = substr($this->id, 10, 2);
			$date->setYear($year);
			$date->setMonth($month);

			$this->navbar->createEntry($year, $this->source.'?id=index_'.$year);
			$this->navbar->createEntry($date->format('%B'));
		}
	}

	// }}}
}

?>
