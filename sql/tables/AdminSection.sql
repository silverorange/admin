create table AdminSection (
	id serial NOT null,
	title varchar(255),
	description varchar(255),
	displayorder integer default 0,
	visible boolean default true not null,
	primary key(id)
);
