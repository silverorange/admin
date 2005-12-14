<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?=$this->title?></title>
	<base href="<?=$this->basehref?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<?=$this->html_head_entries?>
	<?=$this->javascript_includes?>
	<style type="text/css" media="all">@import "admin/styles/admin.css";</style>
</head>
<body id="admin-login-page">

<div id="admin-login">
	<?=$this->ui?>
</div>
<?=$this->login_javascript?>

</body>
</html>
