/**
 * Override the default SwatForm focus behaviour
 */
SwatForm.prototype.setDefaultFocus = function()
{
}

function AdminLogin(login_id, password_id, submit_name, username, login_error)
{
	window.setTimeout(
	"login = document.getElementById('" + login_id + "');"+
	"password = document.getElementById('" + password_id + "');"+
	"submit = document.getElementById('" + submit_name + "');"+
	"if (" + login_error + ") {" +
	"	if (login.value.length && login.value == '" + username + "') {"+
	"		password.focus();"+
	"	} else {"+
	"		login.focus();"+
	"	}"+
	"} else {" +
	"	if (login.value.length > 0 && password.value.length > 0) {" +
	"		submit.focus();" +
	"	} else if (login.value.length > 0 && password.value.length == 0) {" +
	"		password.focus();" +
	"	} else {" +
	"		login.focus();" +
	"	}" +
	"}", 100);
}
