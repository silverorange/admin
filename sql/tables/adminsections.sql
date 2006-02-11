create table adminsections (
	id serial NOT null,
	title varchar(255),
	description varchar(255),
	displayorder integer default 0,
	show boolean default true not null,
	primary key(id)
);

-- default adminsections
insert into adminsections (id, title, description, displayorder, show) values (1, 'Site Content', null, 4, true);
insert into adminsections (id, title, description, displayorder, show) values (2, 'Store Settings', null, 5, true);
insert into adminsections (id, title, description, displayorder, show) values (3, 'Admin Settings', null, 6, true);

