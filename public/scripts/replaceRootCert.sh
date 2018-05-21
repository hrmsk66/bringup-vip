#!/bin/bash

HOST=$1
USER='admin'
PSWD='admin\n'
CERT='/home/admin/cacert.pem'

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
send \"request root-cert-chain install $CERT\n\"
expect {
    default {exit 3}
    \"Successfully installed\" {exit 0}
}
"
R=$?
echo "\n"
echo $R