function AdminMenu(id)
{
	this.client = new XML_RPC_Client('AdminSite/MenuViewServer');
	this.id = id;
	this.init();
}

AdminMenu.preloadImages = function()
{
	if (!AdminMenu.imagesPreloaded) {
		var image1 = new Image();
		var image2 = new Image();
		var image3 = new Image();
		var image4 = new Image();

		image1.src = 'packages/admin/images/admin-menu-section-background.png';
		image2.src = 'packages/admin/images/admin-menu-section-background-closed.png';
		image3.src = 'packages/admin/images/admin-menu-section-open.png';
		image4.src = 'packages/admin/images/admin-menu-section-closed.png';

		AdminMenu.imagesPreloaded = true;
	}
}

AdminMenu.imagesPreloaded = false;
AdminMenu.preloadImages();

AdminMenu.prototype.init = function()
{
	var menu_div = document.getElementById(this.id);
	var closed_sections =
		YAHOO.util.Dom.getElementsByClassName('hide-menu-section', 'li',
			menu_div);

	var section_content;
	for (var i = 0; i < closed_sections.length; i++) {
		section_content = closed_sections[i].firstChild.nextSibling;
		section_content.style.opacity = '0';
		section_content.style.filter = 'alpha(opacity=0)';
	}
}

AdminMenu.prototype.toggle = function()
{
	var body_tag = document.getElementsByTagName('body')[0];
	var shown = false;

	if (YAHOO.util.Dom.hasClass(body_tag, 'hide-menu')) {
		YAHOO.util.Dom.removeClass(body_tag, 'hide-menu');
		shown = true;
	} else {
		YAHOO.util.Dom.addClass(body_tag, 'hide-menu');
	}

	this.client.callProcedure('setShown', null, [shown], ['boolean']);
}

AdminMenu.prototype.toggleSection = function(id)
{
	var section_content = document.getElementById(this.id + '_section_' + id);
	var section_block = section_content.parentNode;
	var shown = false;

	if (YAHOO.util.Dom.hasClass(section_block, 'hide-menu-section')) {
		YAHOO.util.Dom.removeClass(section_block, 'hide-menu-section');
		var attributes = { opacity: { to: 1 } };
		var fade_animation =
			new YAHOO.util.Anim(section_content, attributes, 0.5);

		fade_animation.animate();
		shown = true;
	} else {
		var attributes = { opacity: { to: 0 } };
		var fade_animation =
			new YAHOO.util.Anim(section_content, attributes, 0.25);

		fade_animation.animate();
		fade_animation.onComplete.subscribe(AdminMenu.handleSectionClose,
			section_block);
	}

	this.client.callProcedure('setSectionShown', null,
		[id, shown], ['int', 'boolean']);
}

AdminMenu.handleSectionClose = function(type, args, section_content)
{
	YAHOO.util.Dom.addClass(section_content, 'hide-menu-section');
}
