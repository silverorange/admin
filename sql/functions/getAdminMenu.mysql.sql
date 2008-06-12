CREATE PROCEDURE getAdminMenu(param_userid integer)
	BEGIN
		SELECT AdminComponent.shortname, AdminComponent.title,
			AdminComponent.section, AdminSection.title AS sectiontitle,
			AdminComponent.id,
			AdminSubComponent.title as subcomponent_title,
			AdminSubComponent.shortname as subcomponent_shortname
		FROM AdminComponent

		LEFT OUTER JOIN AdminSubComponent on
			AdminSubComponent.component = AdminComponent.id

		INNER JOIN AdminSection ON
			AdminComponent.section = AdminSection.id

		WHERE AdminSection.visible = true AND
			AdminComponent.enabled = true AND
			AdminComponent.visible = true AND
			(AdminSubComponent.visible = true OR
			AdminSubComponent.visible is null) AND
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
			AdminSubComponent.title;
	END;
