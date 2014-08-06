#!/usr/bin/env bash

find ./data/. -type f -iname "schema.sql" -print0 | while IFS= read -r -d $'\0' line; do
    echo "$line"        
done