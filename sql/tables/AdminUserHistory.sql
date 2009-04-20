create table AdminUserHistory (
	id serial,
	usernum integer not null
		constraint AdminUserHistory_usernum references AdminUser(id)
		on delete cascade,
	login_date timestamp,
	login_agent varchar(255),
	remote_ip varchar(15),
	instance integer references Instance(id) on delete cascade,
	primary key(id)
);

