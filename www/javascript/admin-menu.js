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

AdminMenu.prototype.toggleSection = function(id)
{
	var sectionTag = document.getElementById(id);

	if (sectionTag.className.indexOf('hide-menu-section') == -1) {
		sectionTag.className += ' hide-menu-section';
	} else {
		sectionTag.className =
			sectionTag.className.replace(/hide-menu-section/, '');
	}
}
