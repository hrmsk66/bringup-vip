#!/bin/bash

NEWCERT='./temp/newcert.crt'
CONF='./openssl.cnf'
PASSPHRASE='PASSWORD'
CSR='./temp/csr.pem'

cd rootca
echo "openssl ca -out $NEWCERT -config $CONF -passin pass:$PASSPHRASE -infiles $CSR"
openssl ca -batch -out $NEWCERT -config $CONF -passin pass:$PASSPHRASE -infiles $CSR

echo "\n"
echo $?
