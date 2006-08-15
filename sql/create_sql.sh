#!/bin/sh

WHOAMI=`whoami`
SRC="/so/packages/admin/work-${WHOAMI}/sql"
DST="${SRC}/statements.sql"

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

echo "Dropping the old database"
dropdb -U php $DB
sleep 1

echo "Creating the new database"
createdb -U php -E UTF8 $DB
createlang -U postgres plpgsql $DB
sleep 1

# Create an empty site.
echo "" > $DST

cat ${SRC}/tables/tables.txt | while read LINE; do
	cat ${SRC}/tables/${LINE} >> $DST
done

cat ${SRC}/functions/functions.txt | while read LINE; do
	cat ${SRC}/functions/${LINE} >> $DST
done

cat ${SRC}/views/views.txt | while read LINE; do
	cat ${SRC}/views/${LINE} >> $DST
done

cat ${SRC}/triggers/triggers.txt | while read LINE; do
	cat ${SRC}/triggers/${LINE} >> $DST
done

echo "Creating the generic admin"
psql -U php -f $DST $DB
rm $DST

