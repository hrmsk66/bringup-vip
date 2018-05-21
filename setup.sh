#!/bin/bash

cp example.env .env
php artisan key:generate

cd public/rootca
mkdir newcerts private temp
echo '12345601' > serial
echo 'unique_subject = no' > index.txt.attr
touch index.txt

openssl req -new -x509 -extensions v3_ca -keyout private/cakey.pem -out cacert.pem -days 365 -config ./openssl.cnf -subj "/C=US/ST=California/L=San Jose/OU=vIPtela Test/O=vIPtela Inc/emailAddress=cse-jp-sdwan@cisco.com" -passout pass:PASSWORD
echo 'Done!'
