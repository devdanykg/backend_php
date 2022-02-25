#! /bin/bash

echo Write your username
read name
echo Write your password
read password
echo Write title
read title

account=$( echo -n ${name}:${password} | base64 )

curl \
-H "Authorization: Basic ${account}" \
-H "Content-Type: application/json" \
-X POST \
-k \
-d "{\"title\":\"$title\"}" \
localhost/bank/createBank