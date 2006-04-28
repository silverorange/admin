create table AdminSection (
	id serial NOT null,
	title varchar(255),
	description varchar(255),
	displayorder integer default 0,
	show boolean default true not null,
	primary key(id)
);

insert into AdminSection (id, title, description, displayorder, show) values (1, 'Admin Settings', null, 100, true);

SELECT setval('adminsection_id_seq', max(id)) FROM AdminSection;

