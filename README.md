OAuth2 Auth Server sandbox

## Installation

0. Run `composer install` in this directory to install dependencies
0. Create a private key `openssl genrsa -out private.key 2048`
0. Create a public key `openssl rsa -in private.key -pubout > public.key`

https://host/auth?response_type=token&client_id=50fd4183-eded-11e6-b500-00065bf31ae6&scope=scope1&redirect_uri=https://redirecturi

add scheduled event support for mysql
[mysqld]
event_scheduler=ON
