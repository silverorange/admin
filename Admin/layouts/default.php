<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?=$this->title?></title>
	<base href="<?=$this->basehref?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?=$this->html_head_entries?>
	<style type="text/css" media="all">@import "admin/styles/admin.css";</style>
</head>
<body>

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

	<a href="javascript:menu.toggle();" id="admin-menu-show"><img src="admin/images/admin-menu-show.png" alt="Show Menu" height="86" width="19" /></a>

	<div id="admin-menu">
	<a href="javascript:menu.toggle();" id="admin-menu-hide"><img src="admin/images/admin-menu-hide.png" alt="Hide Menu" height="20" width="87"/></a>
		<?=$this->menu?>
	</div><!-- end admin-menu -->

</div><!-- end admin-body -->


</div><!-- end admin-wrapper -->
</body>
</html>
