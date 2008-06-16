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
		returned_sub_row type_admin_menu%ROWTYPE;
	BEGIN
		FOR returned_row IN
		SELECT AdminComponent.shortname, AdminComponent.title,
			AdminComponent.section, AdminSection.title AS section_title,
			AdminComponent.id,
			null as subcomponent_title,
			null as subcomponent_shortname
		FROM AdminComponent

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
			AdminComponent.title
		LOOP
			FOR returned_sub_row IN
			SELECT AdminComponent.shortname, AdminComponent.title,
				AdminComponent.section, AdminSection.title AS section_title,
				AdminComponent.id,
				AdminSubComponent.title as subcomponent_title,
				AdminSubComponent.shortname as subcomponent_shortname
			FROM AdminSubComponent
				INNER JOIN AdminComponent ON
					AdminSubComponent.component = AdminComponent.id

				INNER JOIN AdminSection ON
					AdminComponent.section = AdminSection.id
			WHERE AdminSubComponent.visible = true AND
				AdminSubComponent.component = returned_row.component_id
			ORDER BY AdminSubComponent.displayorder, AdminSubComponent.title
			LOOP
				RETURN NEXT returned_sub_row;
			END LOOP;
			IF NOT FOUND THEN
				RETURN NEXT returned_row;
			end IF;
		END LOOP;

		RETURN;
	END;
$$ LANGUAGE 'plpgsql';
