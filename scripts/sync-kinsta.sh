#!/bin/zsh

# Syncing Trellis & Bedrock-based WordPress environments with WP-CLI aliases (Kinsta version)
# Version 1.1.0
# Copyright (c) Ben Word

FROM=$1
TO=$2

bold=$(tput bold)
normal=$(tput sgr0)

# Declare arrays to store environment configuration values
SUBDOMAINS=("hq" "tech")
declare -A SOURCE
declare -A DEST
declare -A DEV=(
	["bucket"]="s3://froware-local/uploads"
	["bucketurl"]="https://dzrf8lsyz0jmv.cloudfront.net"
	["rootdomain"]="frocentric.local"
	["domain"]="frocentric.local"
	["url"]="https://frocentric.local"
	["discourseurl"]="https://localhost:4200"
)
declare -A STAGING=(
	["bucket"]="s3://froware-staging/uploads"
	["bucketurl"]="https://d2rk5nbr5qq2m5.cloudfront.net"
	["rootdomain"]="staging.frocentric.io"
	["domain"]="staging.frocentric.io"
	["url"]="https://staging.frocentric.io"
	["discourseurl"]="https://tech-community.staging.frocentric.io"
)
declare -A PRODUCTION=(
	["bucket"]="s3://froware/uploads"
	["bucketurl"]="https://d3kjg0zldfafgn.cloudfront.net"
	["rootdomain"]="frocentric.io"
	["domain"]="www.frocentric.io"
	["url"]="https://www.frocentric.io"
	["discourseurl"]="https://tech.frocentric.io/community"
)

case "$1-$2" in
  production-development) DIR="down ⬇️ "; ;;
  staging-development)    DIR="down ⬇️ "; ;;
  development-production) echo "syncing development to production not supported, sync to staging first. usage: $0 production development | staging development | development staging | staging production | production staging" && exit 1 ;;
  development-staging)    DIR="up ⬆️ "; ;;
  production-staging)     DIR="horizontally ↔️ "; ;;
  staging-production)     DIR="horizontally ↔️ "; ;;
  *) echo "usage: $0 production development | staging development | development staging | staging production | production staging" && exit 1 ;;
esac

case "$1" in
  production)  SOURCE=("${(@fkv)PRODUCTION}"); ;;
  development) SOURCE=("${(@fkv)DEV}"); ;;
  staging)     SOURCE=("${(@fkv)STAGING}"); ;;
esac

case "$2" in
  development) DEST=("${(@fkv)DEV}"); ;;
  production)  DEST=("${(@fkv)PRODUCTION}"); ;;
  staging)     DEST=("${(@fkv)STAGING}"); ;;
esac

read "response?
🔄  Would you really like to ⚠️  ${bold}reset the $TO database${normal} (${DEST[url]})
    and sync ${bold}$DIR${normal} from $FROM (${SOURCE[url]})? [y/N] "

if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
  # Change to site directory
  pwd=$(pwd)
  echo

  # Check we're running under a Bedrock site: https://unix.stackexchange.com/a/22215
  findenv () {
    root=$(pwd)
    while [[ "$root" != "" && ! -e "$root/.env" ]]; do
      root=${root%/*}
    done
    if [[ $root != "" ]]; then
      pushd "$root"
    else
      echo "❌  Unable to find a Bedrock site root"
      exit 1
    fi
  };

  # Make sure both environments are available before we continue
  availfrom() {
    local AVAILFROM
    AVAILFROM=$(wp "@$FROM" option get home 2>&1)
    if [[ $AVAILFROM == *"Error"* ]]; then
      echo "❌  Unable to connect to $FROM"
      exit 1
    else
      echo "✅  Able to connect to $FROM"
    fi
  };

  availto() {
    local AVAILTO
    AVAILTO=$(wp "@$TO" option get home 2>&1)
    if [[ $AVAILTO == *"Error"* ]]; then
      echo "❌  Unable to connect to $TO"
      exit 1
    else
      echo "✅  Able to connect to $TO"
    fi
  };

  sync_db() {
	local DESTSUBDOMAIN
	local SOURCESUBDOMAIN

  	echo

    # Export/import database
    wp "@$TO" db export &&
    wp "@$TO" db reset --yes &&
    wp "@$FROM" db export - | wp "@$TO" db import -

	if [ $? -ne 0 ]; then
		echo "❌  Database import failed" >&2
		exit 1
	fi

	# Retrieve OneAll connection settings for environment
	OA_SOCIAL_LOGIN_SETTINGS_API_KEY=`wp @$TO config get --type=constant OA_SOCIAL_LOGIN_SETTINGS_API_KEY`
	OA_SOCIAL_LOGIN_SETTINGS_API_SECRET=`wp @$TO config get --type=constant OA_SOCIAL_LOGIN_SETTINGS_API_SECRET`
	OA_SOCIAL_LOGIN_SETTINGS_SUBDOMAIN=`wp @$TO config get --type=constant OA_SOCIAL_LOGIN_SETTINGS_SUBDOMAIN`

    # Run search & replace for sub-domains
    for subdomain in "${SUBDOMAINS[@]}"; do
      DESTSUBDOMAIN="$subdomain.${DEST[rootdomain]}"
      SOURCESUBDOMAIN="$subdomain.${SOURCE[rootdomain]}"
	  echo
	  echo "Replacing $SOURCESUBDOMAIN (sub-domain) with $DESTSUBDOMAIN"
      wp @$TO search-replace "$SOURCESUBDOMAIN" "$DESTSUBDOMAIN" --url="$SOURCESUBDOMAIN" &&
      wp @$TO search-replace "$SOURCESUBDOMAIN" "$DESTSUBDOMAIN" --url="${SOURCE[url]}"

	  # Run search & replace for OneAll connection settings
	  OA_SOCIAL_LOGIN_SETTINGS=`wp @$TO option get oa_social_login_settings --url="$DESTSUBDOMAIN" --format=json 2>/dev/null`

	  if [ $? -eq 0 ]; then
		printf '%s\n' "${OA_SOCIAL_LOGIN_SETTINGS}" | php -r "
			\$option = json_decode( fgets(STDIN) );
			\$api_key = \"${OA_SOCIAL_LOGIN_SETTINGS_API_KEY}\";
			\$api_secret = \"${OA_SOCIAL_LOGIN_SETTINGS_API_SECRET}\";
			\$api_subdomain = \"${OA_SOCIAL_LOGIN_SETTINGS_SUBDOMAIN}\";

			if ( ! empty ( \$api_key ) ) {
				\$option->api_key = \$api_key;
			}
			if ( ! empty ( \$api_secret ) ) {
				\$option->api_secret = \$api_secret;
			}
			if ( ! empty ( \$api_subdomain ) ) {
				\$option->api_subdomain = \$api_subdomain;
			}
			print json_encode(\$option);
			" | wp @$TO option set oa_social_login_settings --url="$DESTSUBDOMAIN" --format=json
	  fi
    done

    # Run search & replace for primary domain
	echo
	echo "Replacing ${SOURCE[domain]} (primary domain) with ${DEST[domain]}"
    wp @$TO search-replace "${SOURCE[domain]}" "${DEST[domain]}" --url="${SOURCE[url]}" &&
    wp @$TO search-replace --network "${SOURCE[domain]}" "${DEST[domain]}"

    # Run search & replace for Discourse connection settings
	echo
	echo "Replacing ${SOURCE[discourseurl]} (Discourse URL) with ${DEST[discourseurl]}"
    wp @$TO search-replace "${SOURCE[discourseurl]}" "${DEST[discourseurl]}" --url="${SOURCE[url]}"

  };

  sync_uploads() {
    # Sync buckets
    aws s3 sync "${SOURCE[bucket]}" "${DEST[bucket]}" --cache-control max-age=31536000 --profile frocentric
  };

  # Slack notification when sync direction is up or horizontal
  notify() {
    # if [[ $DIR != "down"* ]]; then
    #   USER="$(git config user.name)"
    #   curl -X POST -H "Content-type: application/json" --data "{\"attachments\":[{\"fallback\": \"\",\"color\":\"#36a64f\",\"text\":\"🔄 Sync from ${SOURCE[url]} to ${DEST[url]} by ${USER} complete \"}],\"channel\":\"#site\"}" https://hooks.slack.com/services/xx/xx/xx
    # fi

    echo -e "\n\n🔄  Sync from $FROM to $TO complete.\n\n    ${bold}${DEST[url]}${normal}\n"
  };

  findenv
  availfrom
  availto
  sync_db
  sync_uploads
  notify

  popd
fi
