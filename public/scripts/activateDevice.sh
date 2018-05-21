#!/bin/bash

HOST=$1
UUID=$2
TOKEN=$3
USER='admin'
PSWD='admin\n'

expect -c "
set timeout 5
spawn ssh -o \"StrictHostKeyChecking no\" -o \"UserKnownHostsFile=/dev/null\" $USER@$HOST
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