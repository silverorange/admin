create table admingroups (
	id serial not null,
	title varchar(255),
	primary key(id)
);

-- default admin groups
insert into admingroups (id, title) values (1, 'silverorange');
