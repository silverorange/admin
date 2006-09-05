create table AdminSubComponent (
	id serial not null,
	component integer not null
		constraint AdminSubcomponent_component references AdminComponent(id)
		on delete cascade,
	title varchar(255),
	shortname varchar(50),
	show boolean default false not null,
	displayorder integer default 0,
	primary key(id)
);

-- default sub-components
insert into AdminSubComponent (id, component, title, shortname, show, displayorder) values (1, 1, 'Login History', 'LoginHistory', true, 0);

SELECT setval('adminsubcomponent_id_seq', max(id)) FROM AdminSubComponent;

