#!/bin/bash

echo Getting schema
../../vendor/bin/propel database:reverse "mysql:host=localhost;dbname=escscsql2;user=root;password=password" --output-dir=. --database-name=escscsql2

echo Updating files
../../vendor/bin/propel build

echo Creating config
../../vendor/bin/propel config:convert-xml