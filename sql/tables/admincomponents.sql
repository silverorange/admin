CREATE TABLE admincomponents (
    id serial NOT NULL,
    shortname character varying(255),
    title character varying(255),
    description text,
    displayorder integer DEFAULT 0,
    section integer NOT NULL references adminsections(id),
    enabled boolean DEFAULT true NOT NULL,
    "show" boolean DEFAULT true NOT NULL,
	primary key(id)
);


INSERT INTO admincomponents (id, shortname, title, description, displayorder, section, enabled, show) VALUES (1, 'AdminUsers', 'Admin Users', NULL, 4, 3, true, true);
INSERT INTO admincomponents (id, shortname, title, description, displayorder, section, enabled, show) VALUES (2, 'AdminGroups', 'Admin Groups', NULL, 5, 3, true, true);
INSERT INTO admincomponents (id, shortname, title, description, displayorder, section, enabled, show) VALUES (3, 'AdminSections', 'Admin Sections', NULL, 3, 3, true, true);
INSERT INTO admincomponents (id, shortname, title, description, displayorder, section, enabled, show) VALUES (4, 'AdminComponents', 'Admin Components', NULL, 1, 3, true, true);
INSERT INTO admincomponents (id, shortname, title, description, displayorder, section, enabled, show) VALUES (5, 'AdminSubComponents', 'Admin Sub-Components', NULL, 2, 3, true, false);
INSERT INTO admincomponents (id, shortname, title, description, displayorder, section, enabled, show) VALUES (6, 'Front', 'Front Page', NULL, 0, 3, true, false);

SELECT pg_catalog.setval('admincomponents_id_seq', 6, true);

CREATE TRIGGER tr_delete
    BEFORE DELETE ON admincomponents
    FOR EACH ROW
    EXECUTE PROCEDURE tr_admincomponents_delete();




