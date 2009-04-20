create or replace view AdminUserLastLoginView as
select usernum, max(login_date) as last_login, instance from AdminUserHistory
	group by usernum, instance;
