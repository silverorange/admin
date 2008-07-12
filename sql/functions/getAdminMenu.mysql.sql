CREATE PROCEDURE getAdminMenu(param_userid integer)
	SELECT AdminComponent.shortname, AdminComponent.title,
		AdminComponent.section, AdminSection.title AS section_title,
		AdminComponent.id AS component_id,
		AdminSubComponent.title AS subcomponent_title,
		AdminSubComponent.shortname AS subcomponent_shortname
	FROM AdminComponent

		LEFT OUTER JOIN AdminSubComponent on
			AdminSubComponent.component = AdminComponent.id and
			AdminSubComponent.visible = true

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
		AdminSubComponent.title;
