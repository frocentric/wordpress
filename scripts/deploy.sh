#! /bin/bash
# Adapted from https://gist.github.com/Prophe1/bf02adbe62e3922093ca23a70c2d35a0

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
        echo "$env_file found"
    else
        echo "$env_file not found"
        exit 1
fi

echo "Deploying $branch_name to $url"
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

# Load HTTP auth from .env file
echo "Looking for $(pwd)/$env_file"
HTTP_USERNAME=$(grep HTTP_USERNAME "$(pwd)/$env_file" | cut -d '=' -f2)

if [ -n "$HTTP_USERNAME" ]; then
    curl -L "$2/kinsta-clear-cache-all/" -u HTTP_USERNAME
else
    curl -L "$2/kinsta-clear-cache-all/"
fi
