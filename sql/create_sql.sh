#!/bin/sh

SRC="/so/packages/admin/work-dave/sql"

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
${SRC}/generate_sql.sh
sleep 1

echo "Dropping the old database"
dropdb -U php $DB
sleep 1

echo "Creating the new database"
createdb -U php -E UTF8 $DB
createlang -U postgres plpgsql $DB
sleep 1

echo "Creating the generic admin"
psql -U php -f ${SRC}/statements.sql $DB
rm ${SRC}/statements.sql

