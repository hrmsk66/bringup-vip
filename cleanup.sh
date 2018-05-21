#!/bin/bash

rm .env

rm ./public/vedgelist
cd public/rootca
rm cacert.pem serial serial.* index.*
rm -r newcerts private temp

echo 'Done!'
