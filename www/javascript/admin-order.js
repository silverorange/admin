/**
 * Adds an order change event handler to a SwatChangeOrder that checks a radio
 * button when the order changes
 *
 * @param String radio_button_id the XHTML id of the radio button to check.
 * @param SwatChangeOrder change_order the change order widget to add event
 *                                      handlers to.
 */
function AdminOrder(radio_button_id, change_order)
{
	this.radio_button = document.getElementById(radio_button_id);
	if (change_order instanceof SwatChangeOrder)
		change_order.order_change_event.subscribe(
			this.orderChangeHandler, this);
}

AdminOrder.prototype.orderChangeHandler = function(type, args, order)
{
	if (order.radio_button)
		order.radio_button.checked = true;
}
