create table adminsections (
	id serial NOT null,
	title varchar(255),
	description varchar(255),
	displayorder integer default 0,
	show boolean default true not null,
	primary key(id)
);

-- default adminsections
insert into adminsections (id, title, description, displayorder, show) values (1, 'Site Content', null, 0, true);
insert into adminsections (id, title, description, displayorder, show) values (2, 'Settings', null, 10, true);
insert into adminsections (id, title, description, displayorder, show) values (3, 'Advanced', null, 20, true);

