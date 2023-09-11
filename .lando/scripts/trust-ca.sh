#!/bin/bash

keychain='/Library/Keychains/System.keychain'
# Add the certificate to the macos trust store
security find-certificate -c "Lando Local CA" $keychain > /dev/null 2>&1
if [ $? != 0 ]; then
    echo "Adding Lando CA to keychain..."
    sudo security add-trusted-cert -d -r trustRoot -k $keychain ~/.lando/certs/lndo.site.pem
else
    echo "Lando CA already exists, nothing to do here."
fi
