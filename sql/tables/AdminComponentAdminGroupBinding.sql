create table AdminComponentAdminGroupBinding (
    component integer not null
		constraint AdminComponentAdminGroupBinding_component references AdminComponent(id)
		on delete cascade,
    groupnum integer not null
		constraint AdminComponentAdminGroupBinding_groupnum references AdminGroup(id)
		on delete cascade,
	primary key(component, groupnum)
);

