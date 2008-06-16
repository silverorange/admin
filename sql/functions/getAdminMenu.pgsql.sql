CREATE TYPE type_admin_menu AS (
	shortname varchar(255),
	title varchar(255),
	section integer,
	section_title varchar(255),
	component_id integer,
	subcomponent_title varchar(255),
	subcomponent_shortname varchar(255)
);

CREATE OR REPLACE FUNCTION getAdminMenu(integer) RETURNS SETOF type_admin_menu AS $$
	DECLARE
		param_userid ALIAS FOR $1;
		returned_row type_admin_menu%ROWTYPE;
	BEGIN
		FOR returned_row IN
		SELECT AdminComponent.shortname, AdminComponent.title,
			AdminComponent.section, AdminSection.title AS sectiontitle,
			AdminComponent.id,
			AdminSubComponent.title as subcomponent_title,
			AdminSubCOmponent.shortname as subcomponent_shortname
		FROM AdminComponent

			LEFT OUTER JOIN AdminSubComponent on
				AdminSubComponent.component = AdminComponent.id and
				AdminSUbComponent.visible = true

			INNER JOIN AdminSection ON
				AdminComponent.section = AdminSection.id

		WHERE AdminSection.visible = true AND
			AdminComponent.enabled = true AND
			AdminComponent.visible = true AND
			AdminComponent.id IN (
			SELECT component
			FROM AdminComponentAdminGroupBinding
				INNER JOIN AdminUserAdminGroupBinding ON
					AdminComponentAdminGroupBinding.groupnum =
						AdminUserAdminGroupBinding.groupnum
			WHERE AdminUserAdminGroupBinding.usernum = param_userid)

		ORDER BY AdminSection.displayorder, AdminSection.title,
			AdminComponent.section, AdminComponent.displayorder,
			AdminComponent.title, AdminSubComponent.displayorder,
			AdminSubComponent.title
		LOOP
			RETURN NEXT returned_row;
		END LOOP;

		RETURN;
	END;
$$ LANGUAGE 'plpgsql';
