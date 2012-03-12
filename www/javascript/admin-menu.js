function AdminMenu(id)
{
	this.id = id;
	this.hover_timer = null;
	this.unhover_timer = null;
	this.hover_active = false;
	this.preloadImages();
	YAHOO.util.Event.onDOMReady(this.init, this, true);
};

/**
 * Time period required to hover over a menu item for tooltips to initially
 * appear. In milliseconds.
 *
 * @var Number
 */
AdminMenu.HOVER_PERIOD = 800;

/**
 * Time period required to hover over a menu item for tooltips to appear
 * after other tooltips have already appeared. In milliseconds.
 *
 * @var Number
 */
AdminMenu.ACTIVE_HOVER_PERIOD = 150;

/**
 * Time period required to not hover over any menu item for the initial
 * hover time to reset. In milliseconds.
 *
 * @var Number
 */
AdminMenu.UNHOVER_PERIOD = 400;

/**
 * Time required for tooltips to fade out when an item is unhovered. In
 * milliseconds.
 *
 * @var Number
 */
AdminMenu.FADE_OUT_PERIOD = 200;

AdminMenu.prototype.preloadImages = function()
{
	var arrow = new Image();
	arrow.src = 'packages/admin/images/admin-menu-help-arrow.png';
};

AdminMenu.prototype.init = function()
{
	this.el = document.getElementById(this.id);
	this.components = YAHOO.util.Dom.getElementsByClassName(
		'admin-menu-component',
		'li',
		this.el,
		function(n)
		{
			YAHOO.util.Event.on(n, 'mouseover', function (e) {
				this.componentMouseOver(e, n);
			}, this, true);
			YAHOO.util.Event.on(n, 'mouseout', function (e) {
				this.componentMouseOut(e, n);
			}, this, true);
		},
		this,
		true
	);
};

AdminMenu.prototype.showHelp = function(li)
{
	var help = this.getHelp(li);

	if (help) {
		if (help._animation && help._animation.isAnimated()) {
			help._animation.stop(false);
		}
		YAHOO.util.Dom.setStyle(help, 'opacity', 1);
		help.style.display = 'block';
	}
};

AdminMenu.prototype.hideHelp = function(li)
{
	var help = this.getHelp(li);

	if (help) {
		if (help._animation && help._animation.isAnimated()) {
			help._animation.stop(false);
		}

		help._animation = new YAHOO.util.Anim(
			help,
			{ opacity: { to: 0 } },
			AdminMenu.FADE_OUT_PERIOD / 1000
		);

		help._animation.onComplete.subscribe(function() {
			help.style.display = 'none';
		});

		help._animation.animate();
	}
};

AdminMenu.prototype.getHelp = function(li)
{
	return YAHOO.util.Dom.getNextSibling(
		YAHOO.util.Dom.getFirstChild(li)
	);
};

AdminMenu.prototype.hasHelp = function(li)
{
	return !!this.getHelp(li);
};

AdminMenu.prototype.onHover = function(li)
{
	this.hover_active = true;
	this.showHelp(li);
};

AdminMenu.prototype.onUnhover = function(li)
{
	this.hover_active = false;
};

AdminMenu.prototype.componentMouseOver = function(e, li)
{
	var target = YAHOO.util.Event.getTarget(e);

	if (this.hasHelp(li)) {

		var that = this;

		var period = (this.hover_active)
			? AdminMenu.ACTIVE_HOVER_PERIOD
			: AdminMenu.HOVER_PERIOD;

		if (this.hover_timer === null) {
			this.hover_timer = setTimeout(function() {
				that.onHover(li);
			}, period);
		}

		if (this.unhover_timer) {
			clearTimeout(this.unhover_timer);
			this.unhover_timer = null;
		}

	}
}

AdminMenu.prototype.componentMouseOut = function(e, li)
{
	var target, all_target;
	var out = true, all_out = true;

	target = all_target = YAHOO.util.Event.getRelatedTarget(e);

	if (target == li) {
		out = false;
	} else if (target !== null) {
		while (target.parentNode) {
			target = target.parentNode;
			if (target == li) {
				out = false;
				break;
			}
		}
	}

	if (YAHOO.util.Dom.hasClass(all_target, 'admin-menu-component')) {
		all_out = false;
	} else if (all_target !== null) {
		while (all_target.parentNode) {
			all_target = all_target.parentNode;
			if (YAHOO.util.Dom.hasClass(all_target, 'admin-menu-component')) {
				all_out = false;
				break;
			}
		}
	}

	if (out && this.hover_timer) {
		clearTimeout(this.hover_timer);
		this.hover_timer = null;
	}

	if (all_out && this.hover_active) {

		var that = this;

		this.unhover_timer = setTimeout(function() {
			that.onUnhover(li);
		}, AdminMenu.UNHOVER_PERIOD);
	}

	if (out && this.hover_active) {
		this.hideHelp(li);
	}
};
