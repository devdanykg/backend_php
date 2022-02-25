#! /bin/bash

echo Write username
read name
echo Write password
read password

curl \
-H "Content-Type: application/json" \
-X POST \
-k \
-d "{\"name\":\"$name\",\"password\":\"$password\"}" \
localhost/user/register