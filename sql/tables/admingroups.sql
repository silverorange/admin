create table admingroups (
	id serial not null,
	title varchar(255),
	primary key(id)
);

-- default admin groups
insert into admingroups (id, title) values (1, 'silverorange');

SELECT setval('admingroups_id_seq', max(id)) FROM admingroups;

