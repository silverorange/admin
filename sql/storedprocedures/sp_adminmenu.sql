CREATE TYPE type_admin_menu AS (
	shortname varchar(255), title varchar(255),
	section integer, sectiontitle varchar(255),
	componentid integer, subcomponent_title  varchar(255),
	subcomponent_shortname varchar(255)
);

CREATE OR REPLACE FUNCTION sp_admin_menu(integer) RETURNS SETOF type_admin_menu AS '

	DECLARE
	param_userid ALIAS FOR $1;
	returned_row type_admin_menu%ROWTYPE;

	BEGIN
	
		FOR returned_row IN
		SELECT admincomponents.shortname, admincomponents.title,
			admincomponents.section, adminsections.title AS sectiontitle,
			admincomponents.componentid,
			adminsubcomponents.title as subcomponent_title,
			adminsubcomponents.shortname as subcomponent_shortname
		FROM admincomponents 

		LEFT OUTER JOIN adminsubcomponents on
			adminsubcomponents.component = admincomponents.componentid

		INNER JOIN adminsections ON
			admincomponents.section = adminsections.sectionid

		WHERE adminsections.show = ''1''

		AND admincomponents.enabled = ''1''
		
		AND admincomponents.show = ''1''


		AND (
			adminsubcomponents.show = ''1''
			OR adminsubcomponents.show is  null
		)
				
		AND admincomponents.componentid IN (
			SELECT component
			FROM admincomponent_admingroup
				INNER JOIN adminuser_admingroup ON
					admincomponent_admingroup.groupnum = adminuser_admingroup.groupnum
			WHERE adminuser_admingroup.usernum = param_userid
		)
				
		ORDER BY adminsections.displayorder, adminsections.title,
			admincomponents.section, admincomponents.displayorder,
			admincomponents.title, adminsubcomponents.displayorder,
			adminsubcomponents.title
		LOOP
			RETURN NEXT returned_row;
		END LOOP;

		RETURN;
	END;
' LANGUAGE 'plpgsql';
