CREATE VIEW AdminUserLastLoginView AS
SELECT usernum, max(logindate) AS lastlogin FROM AdminUserHistory GROUP BY usernum;
