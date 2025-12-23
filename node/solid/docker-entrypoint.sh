#!/bin/sh
set -eu

ASSET_NAMESPACE="${REACTIVE_ASSET_NAMESPACE:-solid}"

if [ -d "/app/dist/client" ]; then
  DEST="/assets-out/${ASSET_NAMESPACE}"
  mkdir -p "${DEST}"
  rm -rf "${DEST:?}/"*
  cp -R /app/dist/client/. "${DEST}/"
fi

exec "$@"

