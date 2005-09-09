create table adminsections (
	id serial NOT null,
	title varchar(255),
	description varchar(255),
	displayorder integer default 0,
	show boolean default true not null,
	primary key(id)
);

-- default adminsections
insert into adminsections (id, title, description, displayorder, show)
	values (11, 'Settings', null, 0, true);

insert into adminsections (id, title, description, displayorder, show)
	values (1, 'Advanced', 'This is a section.', 10, true);

insert into adminsections (id, title, description, displayorder, show)
	values (2, 'Site Content', null, 0, true);
