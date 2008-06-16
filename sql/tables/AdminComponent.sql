CREATE TABLE AdminComponent (
    id serial NOT NULL,
    shortname character varying(255),
    title character varying(255),
    description text,
    displayorder integer DEFAULT 0,
    section integer NOT NULL
		constraint AdminComponent_section references AdminSection(id)
		on delete cascade,
    enabled boolean DEFAULT true NOT NULL,
    visible boolean DEFAULT true NOT NULL,
	primary key(id)
);
