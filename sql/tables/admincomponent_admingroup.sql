create table admincomponent_admingroup (
    component integer not null constraint admincomponent_admingroup_component references admincomponents(id),
    groupnum integer not null constraint admincomponent_admingroup_groupnum references admingroups(id),
	primary key(component, groupnum)
);

-- default admincomponent_admingroup bindings
insert into admincomponent_admingroup (component, groupnum)
	select admincomponents.id, admingroups.id from admincomponents, admingroups;

