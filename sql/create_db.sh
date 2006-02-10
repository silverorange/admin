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

cat ${SOURCE}/tables/adminusers.sql > ./create_db.sql
cat ${SOURCE}/tables/admingroups.sql >> ./create_db.sql
cat ${SOURCE}/tables/adminsections.sql >> ./create_db.sql
cat ${SOURCE}/triggers/tr_admincomponents_delete.sql >> ./create_db.sql
cat ${SOURCE}/tables/admincomponents.sql >> ./create_db.sql
cat ${SOURCE}/tables/adminsubcomponents.sql >> ./create_db.sql
cat ${SOURCE}/tables/adminuserhistory.sql >> ./create_db.sql
cat ${SOURCE}/tables/adminhelp.sql >> ./create_db.sql
cat ${SOURCE}/tables/adminuser_admingroup.sql >> ./create_db.sql
cat ${SOURCE}/tables/admincomponent_admingroup.sql >> ./create_db.sql

#
# VIEWS
#

cat ${SOURCE}/views/view_adminuser_lastlogin.sql >> ./create_db.sql

#
# STORED PROCEDURES
#

cat ${SOURCE}/stored_procedures/sp_admin_menu.sql >> ./create_db.sql

#
# TRIGGERS
#

cat ${SOURCE}/triggers/tr_adminsections_delete.sql >> ./create_db.sql
cat ${SOURCE}/triggers/tr_admincomponents_delete.sql >> ./create_db.sql
cat ${SOURCE}/triggers/tr_admingroups_delete.sql >> ./create_db.sql
cat ${SOURCE}/triggers/tr_adminuserhistory_insert.sql >> ./create_db.sql
cat ${SOURCE}/triggers/tr_adminusers_delete.sql >> ./create_db.sql

