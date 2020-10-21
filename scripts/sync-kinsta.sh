#!/bin/bash

# Syncing Trellis & Bedrock-based WordPress environments with WP-CLI aliases (Kinsta version)
# Version 1.1.0
# Copyright (c) Ben Word

DEVBUCKET="s3://froware-local"
DEVDIR="web/app/uploads/"
DEVSUBSITE="https://froware.frocentric.local"
DEVSITE="https://frocentric.local"
DEVPORT="22"

REMOTEDIR="frowarecom@35.246.15.218:/www/frowarecom_769/public/current/web/app/uploads/"

PRODBUCKET="s3://froware"
PRODPORT="24583"
PRODSUBSITE="https://froware.com"
PRODSITE="https://frocentric.org"

STAGBUCKET="s3://froware-staging"
STAGPORT="18528"
STAGSUBSITE="https://staging.froware.com"
STAGSITE="https://staging.frocentric.org"

FROM=$1
TO=$2

bold=$(tput bold)
normal=$(tput sgr0)

case "$1-$2" in
  production-development) DIR="down ⬇️ "           FROMSITE=$PRODSITE; FROMSUBSITE=$PRODSUBSITE; FROMDIR=$REMOTEDIR; FROMPORT=$PRODPORT; FROMBUCKET=$PRODBUCKET; TOPORT=$DEVPORT; TOSITE=$DEVSITE; TOSUBSITE=$DEVSUBSITE; TODIR=$DEVDIR; TOBUCKET=$DEVBUCKET; ;;
  staging-development)    DIR="down ⬇️ "           FROMSITE=$STAGSITE; FROMSUBSITE=$STAGSUBSITE; FROMDIR=$REMOTEDIR; FROMPORT=$STAGPORT; FROMBUCKET=$STAGBUCKET; TOPORT=$DEVPORT; TOSITE=$DEVSITE; TOSUBSITE=$DEVSUBSITE;  TODIR=$DEVDIR; TOBUCKET=$DEVBUCKET; ;;
  development-production) DIR="up ⬆️ "             FROMSITE=$DEVSITE; FROMSUBSITE=$DEVSUBSITE; FROMDIR=$DEVDIR;  FROMPORT=$DEVPORT; FROMBUCKET=$DEVBUCKET; TOPORT=$PRODPORT; TOSITE=$PRODSITE; TOSUBSITE=$PRODSUBSITE; TODIR=$REMOTEDIR; TOBUCKET=$PRODBUCKET; ;;
  development-staging)    DIR="up ⬆️ "             FROMSITE=$DEVSITE; FROMSUBSITE=$DEVSUBSITE; FROMDIR=$DEVDIR;  FROMPORT=$DEVPORT; FROMBUCKET=$DEVBUCKET; TOPORT=$STAGPORT; TOSITE=$STAGSITE; TOSUBSITE=$STAGSUBSITE; TODIR=$REMOTEDIR; TOBUCKET=$STAGBUCKET; ;;
  production-staging)     DIR="horizontally ↔️ ";  FROMSITE=$PRODSITE; FROMSUBSITE=$PRODSUBSITE; FROMDIR=$REMOTEDIR; FROMPORT=$PRODPORT; FROMBUCKET=$PRODBUCKET; TOPORT=$STAGPORT; TOSITE=$STAGSITE; TOSUBSITE=$STAGSUBSITE; TODIR=$REMOTEDIR; TOBUCKET=$STAGBUCKET; ;;
  staging-production)     DIR="horizontally ↔️ ";  FROMSITE=$STAGSITE; FROMSUBSITE=$STAGSUBSITE; FROMDIR=$REMOTEDIR; FROMPORT=$STAGPORT; FROMBUCKET=$STAGBUCKET; TOPORT=$PRODPORT; TOSITE=$PRODSITE; TOSUBSITE=$PRODSUBSITE; TODIR=$REMOTEDIR; TOBUCKET=$PRODBUCKET; ;;
  *) echo "usage: $0 production development | staging development | development staging | development production | staging production | production staging" && exit 1 ;;
esac

read -r -p "
🔄  Would you really like to ⚠️  ${bold}reset the $TO database${normal} ($TOSITE)
    and sync ${bold}$DIR${normal} from $FROM ($FROMSITE)? [y/N] " response

if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
  # Change to site directory
  pwd=$(pwd)
  echo

  # Check we're running under a Bedrock site: https://unix.stackexchange.com/a/22215
  findenv () {
    path=$(pwd)
    while [[ "$path" != "" && ! -e "$path/.env" ]]; do
      path=${path%/*}
    done
    if [[ $path != "" ]]; then
      cd "$path"
    else
      echo "❌  Unable to find a Bedrock site root"
      exit 1
    fi
  }
  findenv

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
  availfrom

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
  availto
  echo

  # Export/import database
  wp "@$TO" db export &&
  wp "@$TO" db reset --yes &&
  wp "@$FROM" db export - | wp "@$TO" db import -

  # Sync buckets
  aws s3 sync $FROMBUCKET $TOBUCKET --profile frocentric

  # Run search & replace
  wp @development search-replace "staging.frocentric.org" "frocentric.local" --url=https://staging.frocentric.org &&
  wp @development search-replace "staging.froware.com" "froware.frocentric.local" &&
  wp @development search-replace "staging.froware.com" "froware.frocentric.local" --url=https://froware.frocentric.local
  # wp @development site list --field=url | xargs -n1 -I % wp --url=% search-replace "staging.froware.com" "froware.frocentric.local"
#  wp "@$TO" search-replace "$FROMSITE" "$TOSITE" &&
#  wp "@$TO" search-replace "$FROMSUBSITE" "$TOSUBSITE"

  # Slack notification when sync direction is up or horizontal
  # if [[ $DIR != "down"* ]]; then
  #   USER="$(git config user.name)"
  #   curl -X POST -H "Content-type: application/json" --data "{\"attachments\":[{\"fallback\": \"\",\"color\":\"#36a64f\",\"text\":\"🔄 Sync from ${FROMSITE} to ${TOSITE} by ${USER} complete \"}],\"channel\":\"#site\"}" https://hooks.slack.com/services/xx/xx/xx
  # fi
  echo -e "\n\n🔄  Sync from $FROM to $TO complete.\n\n    ${bold}$TOSITE${normal}\n"
  cd "$pwd"
fi
