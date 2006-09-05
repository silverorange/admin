function AdminMenu(id)
{
	this.client = new XML_RPC_Client('AdminSite/MenuViewServer');
	this.id = id;
}

AdminMenu.prototype.toggle = function()
{
	var bodytag = document.getElementsByTagName('body')[0];
	var shown = 0;

	if (bodytag.className.indexOf('hide-menu') == -1) {
		bodytag.className += ' hide-menu';
	} else {
		bodytag.className = bodytag.className.replace(/hide-menu/, '');
		shown = 1;
	}

	this.client.callProcedure('setShown', [shown], null); 
}

AdminMenu.prototype.toggleSection = function(id)
{
	var sectionTag = document.getElementById(this.id + '_section_' + id);
	var shown = 0;

	if (sectionTag.className.indexOf('hide-menu-section') == -1) {
		sectionTag.className += ' hide-menu-section';
	} else {
		sectionTag.className =
			sectionTag.className.replace(/hide-menu-section/, '');

		shown = 1;
	}

	this.client.callProcedure('setSectionShown', [id, shown], null); 
}
