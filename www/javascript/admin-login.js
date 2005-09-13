function adminLogin(login_id, password_id, username) {
	login = document.getElementById(login_id);
	password = document.getElementById(password_id);
	
	if (password.value.length == 0) {
		if (login.value.length && login.value == username)
			password.focus();
		else
			login.focus();
	}
}
