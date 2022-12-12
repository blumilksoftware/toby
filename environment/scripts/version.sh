#!/usr/bin/env sh

# Calculates project version from git state.
# Usage:
#   ./version.sh [-l | --long]
#
# Short format: {latest tag}
# Long format: {latest tag}|{latest commit}-{commit count}({branch name}{dirty indicator})
# Examples:
# 	- v1.3.2 - short format with tag
# 	- dev - short format with no tag
# 	- v1.3.2|a6072b3-27 - clean repo on main branch (or master, or detached head)
# 	- dev|a6072b3-27 - clean repo on main branch if no tags found
# 	- v1.3.2|a6072b3-27(*) - dirty repo on main branch
# 	- v1.3.2|a6072b3-27(#33-payments-refactor) - clean repo on non-main branch
# 	- v1.3.2|a6072b3-27(#33-payments-refactor*) - dirty repo on non-main branch

getLastTag() {
  TAG=$(git describe --tags --abbrev=0 2>/dev/null)

  if [ $? -eq 0 ]; then
    echo "$TAG"
  else
    echo "dev"
  fi
}

isGitRepo() {
  git rev-parse --is-inside-work-tree >/dev/null 2>&1
}

isGitRepo
if [ $? -gt 0 ]; then
  echo "Not a git repository"
  exit 1
fi

gitBranch() {
  BRANCH=$(git rev-parse --abbrev-ref HEAD)

  if [ "$BRANCH" != "main" ] && [ "$BRANCH" != "master" ] && [ "$BRANCH" != "HEAD" ]; then
    echo "$BRANCH"
  fi
}

COMMIT_COUNT=$(git rev-list HEAD --count)
COMMIT_HASH=$(git log --format="%h" -n 1)
LAST_TAG=$(getLastTag)

DIRTY_REPO=$([ -n "$(git status -s)" ] && echo '*')
BRANCH_NAME=$(gitBranch)
BRANCH_INFO=$([ -n "$DIRTY_REPO" ] || [ -n "$BRANCH_NAME" ] && echo "($BRANCH_NAME$DIRTY_REPO)")

if [ $# -eq 1 ]; then
  if [ "$1" = "--long" ] || [ "$1" = "-l" ]; then
    echo "$LAST_TAG|$COMMIT_HASH-$COMMIT_COUNT$BRANCH_INFO"
  else
    echo "Unrecognized flag passed: $1"
    exit 1
  fi
else
  echo "$LAST_TAG"
fi
