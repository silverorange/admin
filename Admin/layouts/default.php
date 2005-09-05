<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?=$this->title?></title>
	<base href="<?=$this->basehref?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?=$this->html_head_entries?>
	<style type="text/css" media="all">@import "admin/admin.css";</style>
</head>
<body>

<div id="admin-header">
	<?=$this->header?>
	<span id="admin-navbar">
		<?=$this->navbar?>
	</span>
</div>

<div id="admin-body">

	<div id="admin-content">
		<?=$this->content?>
	</div>

	<div id="admin-menu">
		<?=$this->menu?>
	</div>

</div>

</body>
</html>
