create table AdminUser (
	id serial not null,
	email varchar(50) not null,
	name varchar(100) not null,
	password varchar(50) not null,
	password_tag varchar(50),
	-- force_password boolean not null default true,
	enabled boolean not null default true,
	menu_state varchar(255),
	primary key(id)
);
