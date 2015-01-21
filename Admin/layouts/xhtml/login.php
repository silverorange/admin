<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<title><?=$this->title?></title>
	<!--if IE]><base href="<?= $this->basehref ?>"></base><![endif]-->
	<!--if !(IE)]><!--><base href="<?= $this->basehref ?>" /><!--<![endif]-->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700,800' rel='stylesheet' type='text/css' />
	<?=$this->html_head_entries?>
</head>
<body <?=$this->body_classes?>>

<?=$this->content?>

</body>
</html>
