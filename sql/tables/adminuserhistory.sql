create table adminuserhistory (
	id serial,
	usernum integer not null
		constraint fk_adminuserhistory_usernum references adminusers(id),
	logindate timestamp,
	loginagent varchar(255),
	remoteip varchar(15),
	primary key(id)
);
