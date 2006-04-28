create table AdminGroup (
	id serial not null,
	title varchar(255),
	primary key(id)
);

-- default admin groups
insert into AdminGroup (id, title) values (1, 'silverorange');

SELECT setval('admingroup_id_seq', max(id)) FROM AdminGroup;

