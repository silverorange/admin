#!/bin/sh

#
# DB
#
# createdb -U php <database>
# createlang -U postgres plpgsql <database>

SOURCE="/so/packages/admin/dbrename/sql"

#
# TABLES
#

cat ${SOURCE}/tables/AdminUser.sql > ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/AdminGroup.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/AdminSection.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/triggers/AdminComponentDeleteTrigger.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/AdminComponent.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/AdminSubComponent.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/AdminUserHistory.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/AdminUserAdminGroupBinding.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/AdminComponentAdminGroupBinding.sql >> ${SOURCE}/create_db.sql

#
# VIEWS
#

cat ${SOURCE}/views/AdminUserLastLoginView.sql >> ${SOURCE}/create_db.sql

#
# FUNCTIONS
#

cat ${SOURCE}/functions/getAdminMenu.sql >> ${SOURCE}/create_db.sql

#
# TRIGGERS
#

cat ${SOURCE}/triggers/AdminSectionDeleteTrigger.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/triggers/AdminGroupDeleteTrigger.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/triggers/AdminUserHistoryInsertTrigger.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/triggers/AdminUserDeleteTrigger.sql >> ${SOURCE}/create_db.sql

