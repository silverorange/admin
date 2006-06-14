CREATE TYPE type_admin_page AS (
	component_title varchar(255),
	shortname varchar(255),
	section_title varchar(255)
);

CREATE OR REPLACE FUNCTION getAdminPage(boolean, varchar, integer) RETURNS SETOF type_admin_page AS '

	DECLARE
	param_enabled ALIAS FOR $1;
	param_shortname ALIAS FOR $2;
	param_userid ALIAS FOR $3;
	
	returned_row type_admin_page%ROWTYPE;

	BEGIN

		FOR returned_row IN
		SELECT AdminComponent.title as component_title,
			AdminComponent.shortname, AdminSection.title as section_title
		FROM AdminComponent

		INNER JOIN AdminSection ON
			AdminComponent.section = AdminSection.id

		WHERE AdminComponent.enabled = param_enabled

		AND AdminComponent.shortname = param_shortname

		AND AdminComponent.id IN (
			SELECT component
			FROM AdminComponentAdminGroupBinding
			INNER JOIN AdminUserAdminGroupBinding
				ON AdminComponentAdminGroupBinding.groupnum =
					AdminUserAdminGroupBinding.groupnum
			WHERE AdminUserAdminGroupBinding.usernum = param_userid
		)
		LOOP
			RETURN NEXT returned_row;
		END LOOP;

		RETURN;
	END;
' LANGUAGE 'plpgsql';
