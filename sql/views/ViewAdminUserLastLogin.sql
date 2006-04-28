CREATE VIEW ViewAdminUserLastLogin AS
SELECT usernum, max(logindate) AS lastlogin FROM AdminUserHistory GROUP BY usernum;
