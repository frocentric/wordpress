#! /bin/bash

if [ -z "$1" ]
    then
        echo "No branch supplied"
        exit 1
fi

if [ -z "$2" ]
    then
        echo "No URL supplied"
        exit 1
fi

branch_name=$1
url=$2
env_file="public/current/.env"

if [ -f "$env_file" ]
    then
        echo ".env file found"
    else
        echo ".env file not found"
        exit 1
fi

echo "Deploying $branch_name"
cd public/current/
git config --global user.email "genyus@gmail.com"
git config --global user.name "Gary McPherson"
git fetch --all
git reset --hard origin/"$branch_name"
git pull origin "$branch_name"
git checkout "$branch_name"
echo "TASK: git pull finished"

composer install --no-dev --optimize-autoloader
echo "TASK: composer install finished"

curl -L "$2/kinsta-clear-cache-all/"