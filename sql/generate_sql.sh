#!/bin/sh

SRC="/so/packages/admin/work-dave/sql"
DST="${SRC}/statements.sql"

#
# TABLES
#

cat ${SRC}/tables/AdminUser.sql > $DST
cat ${SRC}/tables/AdminGroup.sql >> $DST
cat ${SRC}/tables/AdminSection.sql >> $DST
cat ${SRC}/triggers/AdminComponentDeleteTrigger.sql >> $DST
cat ${SRC}/tables/AdminComponent.sql >> $DST
cat ${SRC}/tables/AdminSubComponent.sql >> $DST
cat ${SRC}/tables/AdminUserHistory.sql >> $DST
cat ${SRC}/tables/AdminUserAdminGroupBinding.sql >> $DST
cat ${SRC}/tables/AdminComponentAdminGroupBinding.sql >> $DST

#
# VIEWS
#

cat ${SRC}/views/AdminUserLastLoginView.sql >> $DST

#
# FUNCTIONS
#

cat ${SRC}/functions/getAdminMenu.sql >> $DST

#
# TRIGGERS
#

cat ${SRC}/triggers/AdminSectionDeleteTrigger.sql >> $DST
cat ${SRC}/triggers/AdminGroupDeleteTrigger.sql >> $DST
cat ${SRC}/triggers/AdminUserHistoryInsertTrigger.sql >> $DST
cat ${SRC}/triggers/AdminUserDeleteTrigger.sql >> $DST

