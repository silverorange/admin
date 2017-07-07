<?php

/**
 * @package   Admin
 * @copyright 2017 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class AdminLoginTemplate implements SiteTemplateInterface
{
	// {{{ public function display()

	public function display(SiteLayoutData $data)
	{
		echo <<<'HTML'
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title><?=$data->title?></title>
	<!--if IE]><base href="<?= $data->basehref ?>"></base><![endif]-->
	<!--if !(IE)]><!--><base href="<?= $data->basehref ?>" /><!--<![endif]-->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="icon" href="../favicon.ico" type="image/x-icon" />
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700,800' rel='stylesheet' type='text/css' />
	<?=$data->html_head_entries?>
</head>
<body <?=$data->body_classes?>>

<?=$data->content?>

</body>
</html>

HTML;
	}

	// }}}
}

?>
