#!/bin/sh

SOURCE="/so/packages/admin/work-dave/sql"

if [ -z $1 ]; then
	echo "need destination db name"
	exit 0
else
	DB=$1
fi

clear
echo "Database: $1"
echo
echo

echo "Generating the SQL"
${SOURCE}/create_db.sh
sleep 1

echo "Dropping the old database"
dropdb -U php $DB
sleep 1

echo "Creating the new database"
createdb -U php -E UTF8 $DB
createlang -U postgres plpgsql $DB
sleep 1

echo "Creating the generic admin"
psql -U php -f ${SOURCE}/create_db.sql $DB
rm ${SOURCE}/create_db.sql

