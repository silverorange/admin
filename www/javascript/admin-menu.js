function AdminMenu()
{
	
}


AdminMenu.prototype.toggle = function()
{
	var bodytag = document.getElementsByTagName('body')[0];

	if (bodytag.className.indexOf('hide-menu') == -1) {
		bodytag.className += ' hide-menu';
	} else {
		bodytag.className = bodytag.className.replace(/hide-menu/, '');
	}
}
