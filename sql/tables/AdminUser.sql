create table AdminUser (
	id serial not null,
	email varchar(50) not null,
	name varchar(100) not null,
	password varchar(255) not null,
	password_salt varchar(50),
	password_tag varchar(50),
	password_tag_date timestamp,
	force_change_password boolean not null default true,
	enabled boolean not null default true,
	all_instances boolean not null default false,
	activation_date timestamp,
	primary key(id)
);
