#!/usr/bin/env bash
echo "This will fetch, merge and push three local and remote branches: dev, stable and production!!!"
read -p "Are you sure [y/N] ? " -n 1 -r
echo    # (optional) move to a new line
if [[ $REPLY =~ ^[Yy]$ ]]
then
    # do dangerous stuff
    branch=$(git branch | sed -n -e 's/^\* \(.*\)/\1/p')
    echo "Current branch: ${branch}"

    git add .
    git commit -m update
    git pull

    # pull from remote
    git checkout dev && git pull
    git checkout stable && git pull
    git checkout production && git pull

    # merge
    git checkout dev && git merge stable && git merge production
    git checkout stable && git merge dev && git merge production
    git checkout production && git merge stable && git merge dev

    # push
    git push --all origin

    # checkout back
    git checkout $branch
fi
