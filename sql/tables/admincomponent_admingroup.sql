create table admincomponent_admingroup (
    component integer not null
		constraint admincomponent_admingroup_component references admincomponents(id),
    groupnum integer not null
		constraint admincomponent_admingroup_groupnum references admingroups(id),
	primary key(component, groupnum)
);

-- default admincomponent_admingroup bindings
insert into admincomponent_admingroup (component, groupnum) values (1, 1);
insert into admincomponent_admingroup (component, groupnum) values (2, 1);
insert into admincomponent_admingroup (component, groupnum) values (3, 1);
insert into admincomponent_admingroup (component, groupnum) values (4, 1);
insert into admincomponent_admingroup (component, groupnum) values (5, 1);
insert into admincomponent_admingroup (component, groupnum) values (6, 1);

