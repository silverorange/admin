#!/bin/sh

SOURCE="/so/packages/admin/work-dave/sql"

clear
${SOURCE}/create_db.sh
sleep 1
dropdb -U php mytest
sleep 1
createdb -U php mytest
createlang -U postgres plpgsql mytest
sleep 1
psql -U php -f ${SOURCE}/create_db.sql mytest
rm ${SOURCE}/create_db.sql

