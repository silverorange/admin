/**
 * Override the default SwatForm focus behaviour
 */
SwatForm.prototype.setDefaultFocus = function()
{
}

function AdminLogin(login_id, password_id, submit_name, username)
{
	window.setTimeout(
	"login = document.getElementById('" + login_id + "');"+
	"password = document.getElementById('" + password_id + "');"+
	"submit = document.getElementsByName('" + submit_name + "')[0];"+
	
	"if (password.value.length == 0) {"+
	"	if (login.value.length && login.value == '" + username + "') {"+
	"		password.focus();"+
	"	} else {"+
	"		login.focus();"+
	"	}"+
	"} else {"+
	"	submit.focus();"+
	"}", 0);
}
