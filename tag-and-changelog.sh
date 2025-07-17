#!/bin/bash

# Wrapper script for creating Git tags and automatically updating changelog
# Usage: ./tag-and-changelog.sh <tag_name> [tag_message]

if [ $# -eq 0 ]; then
    echo "Usage: $0 <tag_name> [tag_message]"
    echo "Example: $0 v1.0.2 'Release version 1.0.2'"
    exit 1
fi

TAG_NAME="$1"
TAG_MESSAGE="$2"

echo "Creating Git tag: $TAG_NAME"

# Create the Git tag
if [ -n "$TAG_MESSAGE" ]; then
    git tag -a "$TAG_NAME" -m "$TAG_MESSAGE"
else
    git tag "$TAG_NAME"
fi

# Check if tag creation was successful
if [ $? -eq 0 ]; then
    echo "Tag '$TAG_NAME' created successfully"

    # Update the changelog
    echo "Updating changelog..."
    ./update-changelog.sh --from-tag-creation

    # Update version in README.md
    echo "Updating version in README.md..."
    # Extract version number from tag (remove 'v' prefix if present)
    VERSION_NUMBER=$(echo "$TAG_NAME" | sed 's/^v//')
    sed -i "s/Current version: .*/Current version: $VERSION_NUMBER/" README.md

    # Add the changelog and README.md to git
    git add changelog.txt README.md
    git commit -m "Update changelog and version for $TAG_NAME"

    echo "Changelog updated and committed"
    echo ""
    echo "To push the tag and changelog to remote repository, run:"
    echo "git push origin $TAG_NAME"
    echo "git push origin HEAD"
else
    echo "Failed to create tag '$TAG_NAME'"
    exit 1
fi
