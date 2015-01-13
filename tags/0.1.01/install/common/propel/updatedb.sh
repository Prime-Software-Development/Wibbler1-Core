#!/bin/bash

echo Getting schema
../../vendor/bin/propel database:reverse "mysql:host=db.trunksoftware.co.uk;dbname=dbname;user=root;password=password" --output-dir=. --database-name=dbname

echo Setting Namespace
sed -i.bak 's/<database name="dbname"/<database name="dbname" namespace="DBName"/' ./Radius/schema.xml

echo Updating files
../../vendor/bin/propel build

echo Creating config
../../vendor/bin/propel config:convert-xml

echo Updating composer
cd ../../
composer update