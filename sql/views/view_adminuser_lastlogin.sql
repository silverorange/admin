CREATE VIEW view_adminuser_lastlogin AS
SELECT usernum, max(logindate) AS lastlogin FROM adminuserhistory GROUP BY usernum;
