# Spanish Cow


## Generate the SSH keys :

```
$ mkdir var/jwt
$ openssl genrsa -out var/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in var/jwt/private.pem -out var/jwt/public.pem
```

In case first openssl command forces you to input password use following to get the private key decrypted

```
$ openssl rsa -in var/jwt/private.pem -out var/jwt/private2.pem
$ mv var/jwt/private.pem var/jwt/private.pem-back
$ mv var/jwt/private2.pem var/jwt/private.pem
```