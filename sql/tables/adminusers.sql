create table adminusers (
	id serial not null,
	username varchar(50) not null,
	name varchar(100) not null,
	email varchar(255) not null,
	password varchar(50) not null,
	force_password boolean not null default true,
	enabled boolean not null default true,
	primary key(id)
);

-- default adminusers
insert into adminusers (id, username, name, email, password, enabled)
	values (1, 'nick', 'Nick Burka', 'nick@silverorange.com', 'f12d534540fdc2b0cd8389c4a54ad1fe', true);

insert into adminusers (id, username, name, email, password, enabled)
	values (2, 'nrf', 'Nathan', 'nathan@silverorange.com', 'bfc4fd79ea7fdf1240db016e29ae417a', true);

insert into adminusers (id, username, name, email, password, enabled)
	values (3, 'isaac', 'Isaac', 'isaac@silverorange.com', 'A4478ADF55CCF937A1C9AA86493D56DF', true);

insert into adminusers (id, username, name, email, password, enabled)
	values (4, 'dan', 'Dan', 'dan@silverorange.com', '7497FC213559C07F355432A21C21745A', true);

insert into adminusers (id, username, name, email, password, enabled)
	values (5, 'desroches', 'Stephen DesRoches', 'stephen@silverorange.com', 'f1d5981e34c60eb678516587a27bdb53', true);

insert into adminusers (id, username, name, email, password, enabled)
	values (6, 'gauthierm', 'Mike', 'mike@silverorange.com', '9b5ec8bf901d37eaf29c3c775ecab513', true);

insert into adminusers (id, username, name, email, password, enabled)
	values (7, 'dave', 'Dave', 'dave@silverorange.com', 'ae0ce887676b6d4fa6db438c94daca83', true);

insert into adminusers (id, username, name, email, password, enabled)
	values (8, 'steven', 'Steven', 'steven@silverorange.com', '57f417b438b12da673e661e998e5bb92', true);

insert into adminusers (id, username, name, email, password, enabled)
	values (9, 'dburka', 'Daniel', 'daniel@silverorange.com', '6aecdfe8b004d1e8cb1e42c4414687a9', true);

insert into adminusers (id, username, name, email, password, enabled)
	values (10, 'dennis', 'Dennis', 'dennis@silverorange.com', 'ae0ce887676b6d4fa6db438c94daca83', true);

insert into adminusers (id, username, name, email, password, enabled)
	values (11, 'kelly', 'Kelly', 'kelly@silverorange.com', 'ae0ce887676b6d4fa6db438c94daca83', true);
