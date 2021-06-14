#!/bin/sh
# wait-for-mysql.sh by Martin Osusky

set -e
cmd="$@"

#echo "Wait 5 seconds.. (for background services)"
#sleep 5

echo "Waiting for mysql."

while ! mysqladmin ping -h"$DB_HOST" -P"$DB_PORT" -uroot -p"$DB_PASSWORD" --silent; do
    printf "."
    sleep 1
done

>&2 echo "Mysql is up - executing command"
exec $cmd
