CREATE VIEW AdminUserLastLoginView AS
SELECT usernum, max(login_date) AS last_login FROM AdminUserHistory
	GROUP BY usernum;
