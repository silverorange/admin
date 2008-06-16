create table AdminSubComponent (
	id serial not null,
	component integer not null
		constraint AdminSubcomponent_component references AdminComponent(id)
		on delete cascade,
	title varchar(255),
	shortname varchar(50),
	visible boolean default false not null,
	displayorder integer default 0,
	primary key(id)
);

