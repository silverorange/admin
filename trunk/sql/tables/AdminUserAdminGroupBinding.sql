create table AdminUserAdminGroupBinding (
    usernum integer not null
		constraint AdminUserAdminGroupBinding_usernum references AdminUser(id)
		on delete cascade,
    groupnum integer not null
		constraint AdminUserAdminGroupBinding_groupnum references AdminGroup(id)
		on delete cascade,
	primary key(usernum, groupnum)
);

-- default AdminUserAdminGroupBinding bindings
insert into AdminUserAdminGroupBinding (usernum, groupnum)
	select AdminUser.id, AdminGroup.id from AdminUser, AdminGroup;

