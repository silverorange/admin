insert into AdminSection (id, title, description, displayorder, visible) values (1, 'Admin Settings', null, 100, true);
SELECT setval('Adminsection_id_seq', max(id)) FROM AdminSection;

INSERT INTO AdminComponent (id, shortname, title, description, displayorder, section, enabled, visible)
	VALUES (1, 'AdminUser', 'Admin Users', NULL, 4, 1, true, true);
INSERT INTO AdminComponent (id, shortname, title, description, displayorder, section, enabled, visible)
	VALUES (2, 'AdminGroup', 'Admin Groups', NULL, 5, 1, true, true);
INSERT INTO AdminComponent (id, shortname, title, description, displayorder, section, enabled, visible)
	VALUES (3, 'AdminSection', 'Admin Sections', NULL, 3, 1, true, true);
INSERT INTO AdminComponent (id, shortname, title, description, displayorder, section, enabled, visible)
	VALUES (4, 'AdminComponent', 'Admin Components', NULL, 1, 1, true, true);
INSERT INTO AdminComponent (id, shortname, title, description, displayorder, section, enabled, visible)
	VALUES (5, 'AdminSubComponent', 'Admin Sub-Components', NULL, 2, 1, true, false);
INSERT INTO AdminComponent (id, shortname, title, description, displayorder, section, enabled, visible)
	VALUES (6, 'Front', 'Front Page', NULL, 0, 1, true, false);

SELECT setval('admincomponent_id_seq', max(id)) FROM AdminComponent;

-- default sub-components
insert into AdminSubComponent (id, component, title, shortname, visible, displayorder) values (1, 1, 'Login History', 'LoginHistory', true, 0);

SELECT setval('adminsubcomponent_id_seq', max(id)) FROM AdminSubComponent;

-- default admin groups
insert into AdminGroup (id, title) values (1, 'Default Group');

SELECT setval('admingroup_id_seq', max(id)) FROM AdminGroup;

-- default AdminComponentAdminGroupBinding bindings
insert into AdminComponentAdminGroupBinding (component, groupnum)
	select AdminComponent.id, AdminGroup.id from AdminComponent, AdminGroup;


