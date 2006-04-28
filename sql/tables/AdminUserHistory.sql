create table AdminUserHistory (
	id serial,
	usernum integer not null
		constraint fk_adminuserhistory_usernum references AdminUser(id),
	logindate timestamp,
	loginagent varchar(255),
	remoteip varchar(15),
	primary key(id)
);

