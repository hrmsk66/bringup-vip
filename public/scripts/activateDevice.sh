#!/bin/bash

HOST=$1
PORT=$2
UUID=$3
TOKEN=$4
USER='admin'
PSWD='admin\n'

expect -c "
set timeout 5
spawn ssh -p $PORT -o \"StrictHostKeyChecking no\" -o \"UserKnownHostsFile=/dev/null\" $USER@$HOST
expect {
    default {exit 2}
    \"$USER@$HOST's password: \" {
        send \"$PSWD\"
    }
}
expect \"admin connected from\"
send \"\n\"
expect \"#\"
send \"request vedge-cloud activate chassis-number $UUID token $TOKEN\n\"
expect {
    default {exit 3}
    \"#\" {exit 0}
}
"
R=$?
echo "\n"
echo $R