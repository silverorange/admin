#!/bin/sh

SOURCE="/so/packages/admin/work-dave/sql"

clear
${SOURCE}/create_db.sh
sleep 1
dropdb -U php veseys2_import
sleep 1
createdb -U php -E UTF8 veseys2_import
createlang -U postgres plpgsql veseys2_import
sleep 1
psql -U php -f ${SOURCE}/create_db.sql veseys2_import
rm ${SOURCE}/create_db.sql

