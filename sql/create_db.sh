#!/bin/sh

#
# DB
#
# createdb -U php <database>
# createlang -U postgres plpgsql <database>

#
# TABLES
#
ADMINDIR="/so/packages/admin/work-dave/sql"

cat ${ADMINDIR}/tables/adminusers.sql > ./create_db.sql
cat ${ADMINDIR}/tables/admingroups.sql >> ./create_db.sql
cat ${ADMINDIR}/tables/adminsections.sql >> ./create_db.sql
cat ${ADMINDIR}/triggers/tr_admincomponents_delete.sql >> ./create_db.sql
cat ${ADMINDIR}/tables/admincomponents.sql >> ./create_db.sql
cat ${ADMINDIR}/tables/adminsubcomponents.sql >> ./create_db.sql
cat ${ADMINDIR}/tables/adminuserhistory.sql >> ./create_db.sql
cat ${ADMINDIR}/tables/adminhelp.sql >> ./create_db.sql
cat ${ADMINDIR}/tables/adminuser_admingroup.sql >> ./create_db.sql
cat ${ADMINDIR}/tables/admincomponent_admingroup.sql >> ./create_db.sql

#
# VIEWS
#

cat ${ADMINDIR}/views/view_adminuser_lastlogin.sql >> ./create_db.sql

#
# STORED PROCEDURES
#

cat ${ADMINDIR}/stored_procedures/sp_admin_menu.sql >> ./create_db.sql

#
# TRIGGERS
#

cat ${ADMINDIR}/triggers/tr_adminsections_delete.sql >> ./create_db.sql
cat ${ADMINDIR}/triggers/tr_admincomponents_delete.sql >> ./create_db.sql
cat ${ADMINDIR}/triggers/tr_admingroups_delete.sql >> ./create_db.sql
cat ${ADMINDIR}/triggers/tr_adminuserhistory_insert.sql >> ./create_db.sql
cat ${ADMINDIR}/triggers/tr_adminusers_delete.sql >> ./create_db.sql

