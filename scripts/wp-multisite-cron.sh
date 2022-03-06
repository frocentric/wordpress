#!/bin/bash

# This script runs all due events on every WordPress site in a Multisite install, using WP CLI.
# To be run by the "www-data" user every minute or so.
#
# Adapted from https://tech.chrishardie.com/2020/running-wordpress-cron-on-a-multisite-instance/
# Thanks https://geek.hellyer.kiwi/2017/01/29/using-wp-cli-run-cron-jobs-multisite-networks/
SCRIPT_PATH="$( cd "$( dirname "$( dirname "${BASH_SOURCE[0]}" )" )" && pwd )"
PATH_TO_WORDPRESS="${SCRIPT_PATH}/web/wp"
DEBUG_LOG=/var/log/wp-cron

if [[ $1 == "--debug" ]]; then
	echo wordpress dir: $PATH_TO_WORDPRESS
	DEBUG=true
else
	DEBUG=false
fi

if [ "$DEBUG" = true ]; then
	echo $(date -u) "Running WP Cron for all sites." >> $DEBUG_LOG
fi

for URL in $(wp site list --field=url --path="${PATH_TO_WORDPRESS}" --deleted=0 --archived=0)
do
	if [[ $URL == "http"* ]]; then
		if [ "$DEBUG" = true ]; then
			echo $(date -u) "Running WP Cron for $URL:" >> $DEBUG_LOG
			wp cron event run --due-now --url="$URL" --path="$PATH_TO_WORDPRESS" >> $DEBUG_LOG
		else
			wp cron event run --quiet --due-now --url="$URL" --path="$PATH_TO_WORDPRESS"
		fi
	fi
done

echo "wp-multisite-cron completed at $(date -u)"
