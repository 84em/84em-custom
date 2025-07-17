#!/bin/bash

# Script to generate changelog.txt from Git log
# This script creates a changelog organized by tags/releases

CHANGELOG_FILE="changelog.txt"

echo "Generating changelog from Git log..."

# Create or overwrite the changelog file
cat > "$CHANGELOG_FILE" << 'EOF'
# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

EOF

# Get all tags sorted by version (most recent first)
TAGS=$(git tag --sort=-version:refname)

# If no tags exist, show all commits
if [ -z "$TAGS" ]; then
    echo "## [Unreleased]" >> "$CHANGELOG_FILE"
    echo "" >> "$CHANGELOG_FILE"
    git log --pretty=format:"- %s (%h)" --reverse >> "$CHANGELOG_FILE"
    echo "" >> "$CHANGELOG_FILE"
else
    # Get commits since the latest tag (unreleased changes)
    LATEST_TAG=$(echo "$TAGS" | head -n1)
    UNRELEASED_COMMITS=$(git log "$LATEST_TAG"..HEAD --pretty=format:"- %s (%h)")

    if [ ! -z "$UNRELEASED_COMMITS" ]; then
        echo "## [Unreleased]" >> "$CHANGELOG_FILE"
        echo "" >> "$CHANGELOG_FILE"
        echo "$UNRELEASED_COMMITS" >> "$CHANGELOG_FILE"
        echo "" >> "$CHANGELOG_FILE"
    fi

    # Process each tag
    TAG_ARRAY=($TAGS)
    for i in "${!TAG_ARRAY[@]}"; do
        TAG="${TAG_ARRAY[$i]}"
        # Get tag date
        TAG_DATE=$(git log -1 --format=%ai "$TAG" | cut -d' ' -f1)

        echo "## [$TAG] - $TAG_DATE" >> "$CHANGELOG_FILE"
        echo "" >> "$CHANGELOG_FILE"

        # Get commits for this tag
        if [ $i -eq $((${#TAG_ARRAY[@]} - 1)) ]; then
            # Last (oldest) tag - get all commits up to this tag
            git log --pretty=format:"- %s (%h)" --reverse "$TAG" >> "$CHANGELOG_FILE"
        else
            # Get commits between this tag and the previous (older) tag
            PREV_TAG="${TAG_ARRAY[$((i + 1))]}"
            git log --pretty=format:"- %s (%h)" --reverse "$PREV_TAG".."$TAG" >> "$CHANGELOG_FILE"
        fi

        echo "" >> "$CHANGELOG_FILE"
        echo "" >> "$CHANGELOG_FILE"
    done
fi

echo "Changelog generated successfully in $CHANGELOG_FILE"
