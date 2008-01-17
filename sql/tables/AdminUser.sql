create table AdminUser (
	id serial not null,
	email varchar(50) not null,
	name varchar(100) not null,
	password varchar(50) not null,
	password_salt varchar(50) not null,
	password_tag varchar(50),
	force_change_password boolean not null default true,
	enabled boolean not null default true,
	menu_state varchar(255),
	instance integer not null references Instance(id);
	primary key(id)
);
