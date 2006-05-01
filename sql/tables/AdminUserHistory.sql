create table AdminUserHistory (
	id serial,
	usernum integer not null
		constraint fk_adminuserhistory_usernum references AdminUser(id),
	login_date timestamp,
	login_agent varchar(255),
	remote_ip varchar(15),
	primary key(id)
);

