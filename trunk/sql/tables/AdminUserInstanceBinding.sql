create table AdminUserInstanceBinding (
	usernum int not null references AdminUser(id) on delete cascade,
	instance int not null references Instance(id) on delete cascade,
	primary key (usernum, instance)
);
