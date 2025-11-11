#!/bin/sh
set -eu

ASSET_NAMESPACE="${REACTIVE_ASSET_NAMESPACE:-next}"

if [ -d "/app/.next/static" ]; then
  DEST="/assets-out/${ASSET_NAMESPACE}/_next/static"
  mkdir -p "${DEST}"
  rm -rf "${DEST}"
  mkdir -p "${DEST}"
  cp -R /app/.next/static/. "${DEST}/"
fi

exec "$@"

