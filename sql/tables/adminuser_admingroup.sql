create table adminuser_admingroup (
    usernum integer not null constraint adminuser_admingroup_usernum references adminusers(id),
    groupnum integer not null constraint adminuser_admingroup_groupnum references admingroups(id),
	primary key(usernum, groupnum)
);

-- default adminuser_admingroup bindings
insert into adminuser_admingroup (usernum, groupnum) values (1, 1);
insert into adminuser_admingroup (usernum, groupnum) values (2, 1);
insert into adminuser_admingroup (usernum, groupnum) values (3, 1);
insert into adminuser_admingroup (usernum, groupnum) values (4, 1);
insert into adminuser_admingroup (usernum, groupnum) values (5, 1);
insert into adminuser_admingroup (usernum, groupnum) values (6, 1);
insert into adminuser_admingroup (usernum, groupnum) values (7, 1);
insert into adminuser_admingroup (usernum, groupnum) values (8, 1);
insert into adminuser_admingroup (usernum, groupnum) values (9, 1);
insert into adminuser_admingroup (usernum, groupnum) values (10, 1);
insert into adminuser_admingroup (usernum, groupnum) values (11, 1);

