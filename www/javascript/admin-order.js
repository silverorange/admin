/**
 * Adds mousedown event handlers to the items of a SwatChangeOrder that
 * checks a radio button
 *
 * @param String radio_button_id the XHTML id of the radio button to check.
 * @param SwatChangeOrder change_order the change order widget to add event
 *                                      handlers to.
 */
function AdminOrder(radio_button_id, change_order)
{
	var is_ie = (document.addEventListener) ? false : true;
	var radio_button = document.getElementById(radio_button_id);
	var node;

	for (var i = 0; i < change_order.list_div.childNodes.length; i++) {
		node = change_order.list_div.childNodes[i];
		if (is_ie)
			node.attachEvent('onmousedown', mousedownEventHandler);
		else
			node.addEventListener('mousedown', mousedownEventHandler, false);
	}

	function mousedownEventHandler(event)
	{
		radio_button.checked = true;
		return false;
	}
}
