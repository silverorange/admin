<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?=$this->title?></title>
	<base href="<?=$this->basehref?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?=$this->html_head_entries?>
</head>
<body class="<?= implode(' ', $this->body_classes->getArrayCopy()) ?>">

<div id="doc3" class="yui-t1">

<div id="hd">

	<?=$this->header?>
	<div id="admin-navbar">
		<?=$this->navbar?>
	</div><!-- end admin-navbar -->

</div><!-- end #hd -->

<div id="bd">

	<div id="yui-main">
		<div class="yui-b">
			<?=$this->content?>
		</div><!-- end admin-content -->
	</div><!-- end #yui-main -->

	<div class="yui-b">
	<?=$this->menu?>
	</div>

</div><!-- end #bd -->

</div><!-- end #doc -->
</body>
</html>
