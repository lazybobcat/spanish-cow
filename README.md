# Spanish Cow

## Installation

Install composer vendor:

```
$ php composer install
```

If you want a super-admin user, use:

```
$ php bin/console app:user:create --super-admin
```

Initialize database with fixtures:

```
# If the database is not created yet:
$ php bin/console doctrine:database:create
$ php bin/console doctrine:migrations:migrate --no-interaction
$ php bin/console doctrine:fixtures:load
```

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

## Get JWT token

```
curl -X POST <base_url>/api/login_check -d _username=<email> -d _password=<password>
```