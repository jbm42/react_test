#!/bin/sh
set -eu

if [ -d "/app/dist/client" ]; then
  mkdir -p /assets-out
  rm -rf /assets-out/*
  cp -R /app/dist/client/. /assets-out/
fi

exec "$@"

