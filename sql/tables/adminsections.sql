create table adminsections (
	id serial NOT null,
	title varchar(255),
	description varchar(255),
	displayorder integer default 0,
	show boolean default true not null,
	primary key(id)
);

insert into adminsections (id, title, description, displayorder, show) values (1, 'Admin Settings', null, 100, true);

SELECT setval('adminsections_id_seq', max(id)) FROM adminsections;

