#!/bin/sh

#
# DB
#
# createdb -U php <database>
# createlang -U postgres plpgsql <database>

SOURCE="/so/packages/admin/work-dave/sql"

#
# TABLES
#

cat ${SOURCE}/tables/adminusers.sql > ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/admingroups.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/adminsections.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/triggers/tr_admincomponents_delete.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/admincomponents.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/adminsubcomponents.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/adminuserhistory.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/adminuser_admingroup.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/tables/admincomponent_admingroup.sql >> ${SOURCE}/create_db.sql

#
# VIEWS
#

cat ${SOURCE}/views/view_adminuser_lastlogin.sql >> ${SOURCE}/create_db.sql

#
# STORED PROCEDURES
#

cat ${SOURCE}/stored_procedures/sp_admin_menu.sql >> ${SOURCE}/create_db.sql

#
# TRIGGERS
#

cat ${SOURCE}/triggers/tr_adminsections_delete.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/triggers/tr_admincomponents_delete.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/triggers/tr_admingroups_delete.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/triggers/tr_adminuserhistory_insert.sql >> ${SOURCE}/create_db.sql
cat ${SOURCE}/triggers/tr_adminusers_delete.sql >> ${SOURCE}/create_db.sql

