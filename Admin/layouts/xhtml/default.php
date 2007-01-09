<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?=$this->title?></title>
	<base href="<?=$this->basehref?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?=$this->html_head_entries?>
</head>
<body class="<?= implode(' ', $this->body_classes->getArrayCopy()) ?>">

<div id="admin-wrapper">

<div id="admin-header">
	<?=$this->header?>
	<div id="admin-navbar">
		<?=$this->navbar?>
	</div><!-- end admin-navbar -->
</div><!-- end admin-header -->

<div id="admin-body">

	<div id="admin-content">
		<?=$this->content?>
	</div><!-- end admin-content -->

	<?=$this->menu?>

</div><!-- end admin-body -->


</div><!-- end admin-wrapper -->
</body>
</html>
