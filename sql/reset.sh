#!/bin/sh

SOURCE="/so/packages/admin/work-dave/sql"
DB="veseys2_import"

clear
${SOURCE}/create_db.sh
sleep 1
dropdb -U php $DB
sleep 1
createdb -U php -E UTF8 $DB
createlang -U postgres plpgsql $DB
sleep 1
psql -U php -f ${SOURCE}/create_db.sql $DB
rm ${SOURCE}/create_db.sql

