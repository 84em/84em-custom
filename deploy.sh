#!/bin/sh
# This command synchronizes the local 84em-custom plugin directory with a remote server using rsync
# -a: archive mode (preserves permissions, timestamps, etc.)
# -v: verbose output showing the transfer progress
# -z: compresses the data during transfer
# Source: Local plugin directory at /home/andrew/workspace/84em/app/public/wp-content/plugins/84em-custom/
# Destination: Remote server (84em) at /www/g84emcom_126/public/wp-content/plugins/84em-custom

echo "Deploying to remote server..."
rsync -avz --exclude 'deploy.sh' --exclude '.git' --exclude 'node_modules' --exclude 'update-changelog.sh' --exclude 'tag-and-changelog.sh' /home/andrew/workspace/84em/app/public/wp-content/plugins/84em-custom/ 84em:/www/g84emcom_126/public/wp-content/plugins/84em-custom
