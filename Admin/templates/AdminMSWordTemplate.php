<?php

/**
 * @package   Admin
 * @copyright 2017 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminMSWordTemplate implements SiteTemplateInterface
{
	// {{{ public function display()

	public function display(SiteLayoutData $data)
	{
		echo <<<'HTML'
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<body>

<?php
header('Content-Type: application/msword');
header('Content-Disposition: attachment; filename='.$data->filename.'.doc');
echo $data->content;
?>

</body>
</html>

HTML;
	}

	// }}}
}

?>
