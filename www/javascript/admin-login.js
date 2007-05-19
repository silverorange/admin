/**
 * Override the default SwatForm focus behaviour
 */
SwatForm.prototype.setDefaultFocus = function()
{
}

function AdminLogin(email_id, password_id, submit_name, user_email, login_error)
{
	setTimeout(
	"email = document.getElementById('" + email_id + "');"+
	"password = document.getElementById('" + password_id + "');"+
	"submit = document.getElementById('" + submit_name + "');"+
	"if (" + login_error + ") {" +
	"	if (email.value.length && email.value == '" + user_email + "') {"+
	"		password.focus();"+
	"	} else {"+
	"		email.focus();"+
	"	}"+
	"} else {" +
	"	if (email.value.length > 0 && password.value.length > 0) {" +
	"		submit.focus();" +
	"	} else if (email.value.length > 0 && password.value.length == 0) {" +
	"		password.focus();" +
	"	} else {" +
	"		email.focus();" +
	"	}" +
	"}", 100);
}
