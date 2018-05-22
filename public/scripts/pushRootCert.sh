#!/bin/bash

USER='admin'
PSWD='admin\n'
CERT='./rootca/temp/cacert.pem'
HOST=$1
PORT=$2

echo "sending CA cert to $HOST"

expect -c "
set timeout 5
spawn scp -P $PORT -o \"StrictHostKeyChecking no\" -o \"UserKnownHostsFile=/dev/null\" $CERT $USER@$HOST:~/
expect {
    default {exit 2}
    \"$USER@$HOST's password: \" {
        send \"$PSWD\"
    }
}
expect {
    default {exit 3}
    \"100%\" {exit 0}
}
"
R=$?
echo $R