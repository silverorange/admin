create table adminuser_admingroup (
    usernum integer not null constraint adminuser_admingroup_usernum references adminusers(id),
    groupnum integer not null constraint adminuser_admingroup_groupnum references admingroups(id),
	primary key(usernum, groupnum)
);

-- default adminuser_admingroup bindings
insert into adminuser_admingroup (usernum, groupnum)
	select adminusers.id, admingroups.id from adminusers, admingroups;

