CREATE TABLE AdminComponent (
    id serial NOT NULL,
    shortname character varying(255),
    title character varying(255),
    description text,
    displayorder integer DEFAULT 0,
    section integer NOT NULL references AdminSection(id),
    enabled boolean DEFAULT true NOT NULL,
    "show" boolean DEFAULT true NOT NULL,
	primary key(id)
);


INSERT INTO AdminComponent (id, shortname, title, description, displayorder, section, enabled, show)
	VALUES (1, 'AdminUsers', 'Admin Users', NULL, 4, 1, true, true);
INSERT INTO AdminComponent (id, shortname, title, description, displayorder, section, enabled, show)
	VALUES (2, 'AdminGroups', 'Admin Groups', NULL, 5, 1, true, true);
INSERT INTO AdminComponent (id, shortname, title, description, displayorder, section, enabled, show)
	VALUES (3, 'AdminSections', 'Admin Sections', NULL, 3, 1, true, true);
INSERT INTO AdminComponent (id, shortname, title, description, displayorder, section, enabled, show)
	VALUES (4, 'AdminComponents', 'Admin Components', NULL, 1, 1, true, true);
INSERT INTO AdminComponent (id, shortname, title, description, displayorder, section, enabled, show)
	VALUES (5, 'AdminSubComponents', 'Admin Sub-Components', NULL, 2, 1, true, false);
INSERT INTO AdminComponent (id, shortname, title, description, displayorder, section, enabled, show)
	VALUES (6, 'Front', 'Front Page', NULL, 0, 1, true, false);

SELECT setval('admincomponent_id_seq', max(id)) FROM AdminComponent;

CREATE TRIGGER tr_delete
    BEFORE DELETE ON AdminComponent
    FOR EACH ROW
    EXECUTE PROCEDURE AdminComponentDeleteTrigger();

