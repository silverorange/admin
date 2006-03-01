<?php

require_once 'Admin/pages/AdminPage.php';

/**
 * Page for Webalizer component
 *
 * @package   Admin
 * @copyright 2006 silverorange
 */
class WebalizerIndex extends AdminPage
{
	private $siteroot = '';
	private $sitename = '';
	
	// init phase
	// {{{ protected function initInternal()

	protected function initInternal()
	{
		$this->ui->loadFromXML(dirname(__FILE__).'/index.xml');
	}

	// }}}

	// build phase
	// {{{ protected function buildInternal()

	protected function buildinternal()
	{
		parent::buildinternal();

		$stats_content = $this->ui->getWidget('stats_content');

		ob_start();
		$this->displayStats();
		$stats_content->content = ob_get_clean();
	}

	// }}}
	// {{{ private function displayStats()

	protected function displayStats()
	{
		$this->sitename = preg_replace('/admin$/', '', $this->app->id);
		$this->siteroot = '/so/webalizer/www/'.$this->sitename;
		
		if (isset($_GET['year'])) {
			$filename = $this->siteroot.'/index_'.$_GET['year'].'.html';
			$this->outputFile($filename);
		} else if (isset($_GET['file'])) {
			$filename = $this->siteroot.'/'.$_GET['file'];
			$this->outputFile($filename);
		} else {
			$links = $this->buildYearIndex();
			arsort($links);

			foreach ($links as $link) {
				$year = substr($link, 6, 4);
				echo '<a href="Webalizer?year='.$year.'">'.$year.'</a><br>';
			}
		}
	}

	// }}}
	// {{{ private function buildYearIndex()

	private function buildYearIndex()
	{
		$matches = $this->scanDir($this->siteroot, '/^index_[0-9]{4}\.html$/i', 'name', 1);
		return $matches;
	}

	// }}}
	// {{{ private function scanDir()

	private function scanDir($directory = '/tmp', $expression = '.*', $how = 'name')
	{
		$matches = array();
		$dirhandle = opendir($directory);

		if ($dirhandle) {
			while (($filename = readdir($dirhandle)) !== false) {
				if (preg_match($expression, $filename)) {
					$stat = stat("$directory/$filename");
					$matches[$filename] = ($how == 'name') ? $filename: $stat[$how];
				}
			}

			closedir($dirhandle);
	   }

	   return(array_keys($matches));
	}

	// }}}
	// {{{ private function outputIndex()

	private function outputFile($filename = '/tmp/index.html')
	{
		if ($filehandle = fopen($filename, "r")) {
			$buffer = file_get_contents($filename);
			$lines = explode("\n", $buffer);

			echo '<div id="stats">';

			foreach ($lines as $line) {
				if (eregi("usage_", $line)) {
					$line = eregi_replace('(usage_.*\.html)', 'Webalizer?file=\\1', $line);
				}

				if (eregi("img src=\"", $line)) {
					$line = eregi_replace('img src="', 'img src="images/webalizer_'.$this->sitename.'/', $line);
				}

				echo $line;
			}

			echo '</div>';
		} else {
			echo "File can not be found";
		}
	}

	// }}}
}

?>
