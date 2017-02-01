CREATE TABLE `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(255) NOT NULL COMMENT 'Client UUID',
  `name` varchar(255) NOT NULL COMMENT 'Client/App name',
  `client_secret` varchar(2000) DEFAULT NULL COMMENT 'Encrypted Client Secret',
  `redirect_urls` varchar(2000) DEFAULT NULL COMMENT 'redirect URI or a serialiazed indexed array of redirect URIs',
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_id` (`client_id`)
);

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL COMMENT 'User UUID',
  `username` varchar(255) NOT NULL COMMENT 'Login name',
  `password` varchar(2000) NOT NULL COMMENT 'Encrypted Password',
  `redirect_urls` varchar(2000) DEFAULT NULL COMMENT 'redirect URI or a serialiazed indexed array of redirect URIs',
  PRIMARY KEY (`id`)
);

CREATE TABLE `scopes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(1000) NOT NULL COMMENT 'Scope',
  `scope_is_default` BOOLEAN NOT NULL DEFAULT 'false' COMMENT 'Force to add it to scopes, even if it is not requested',
  `description` varchar(2000) DEFAULT NULL COMMENT 'Description of the scope',
  PRIMARY KEY (`id`)
);

CREATE TABLE `relations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `clients_id` bigint(20) unsigned NOT NULL,
  `users_id` bigint(20) unsigned NOT NULL,
  `scopes_id` varchar(1000) NOT NULL COMMENT 'Scope',
  `grant_type` ENUM('authorization_code','client_credentials','password','implicit') NOT NULL COMMENT 'Grant Type',
  `token_type` ENUM('Bearer','pop','jwt') NOT NULL DEFAULT 'Bearer' COMMENT 'Token Type',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`clients_id`) REFERENCES `clients` (`id`),
  FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  FOREIGN KEY (`scopes_id`) REFERENCES `scopes` (`id`)
);

CREATE TABLE `auth_code` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(1000) NOT NULL COMMENT 'Token',
  `created` timestamp NOT NULL DEAFAULT CURRENT_TIMESTAMP COMMENT 'Creation Timestamp',
  `revoked` timestamp DEAFAULT NULL COMMENT 'Revocation Timestamp',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`relations`) REFERENCES `relations` (`id`),
);

CREATE TABLE `access_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(1000) NOT NULL COMMENT 'Token',
  `created` timestamp NOT NULL DEAFAULT CURRENT_TIMESTAMP COMMENT 'Creation Timestamp',
  `revoked` timestamp DEAFAULT NULL COMMENT 'Revocation Timestamp',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`relations`) REFERENCES `relations` (`id`),
);

CREATE TABLE `refresh_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(1000) NOT NULL COMMENT 'Token',
  `created` timestamp NOT NULL DEAFAULT CURRENT_TIMESTAMP COMMENT 'Creation Timestamp',
  `revoked` timestamp DEAFAULT NULL COMMENT 'Revocation Timestamp',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`relations`) REFERENCES `relations` (`id`),
);


/*
TODO: Clean this..

CREATE TABLE oauth_access_tokens (access_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT access_token_pk PRIMARY KEY (access_token));

CREATE TABLE oauth_clients (client_id VARCHAR(80) NOT NULL, client_secret VARCHAR(80), redirect_uri VARCHAR(2000) NOT NULL, grant_types VARCHAR(80), scope VARCHAR(100), user_id VARCHAR(80), CONSTRAINT clients_client_id_pk PRIMARY KEY (client_id));
CREATE TABLE oauth_access_tokens (access_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT access_token_pk PRIMARY KEY (access_token));
CREATE TABLE oauth_authorization_codes (authorization_code VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), redirect_uri VARCHAR(2000), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT auth_code_pk PRIMARY KEY (authorization_code));
CREATE TABLE oauth_refresh_tokens (refresh_token VARCHAR(40) NOT NULL, client_id VARCHAR(80) NOT NULL, user_id VARCHAR(255), expires TIMESTAMP NOT NULL, scope VARCHAR(2000), CONSTRAINT refresh_token_pk PRIMARY KEY (refresh_token));
CREATE TABLE oauth_users (username VARCHAR(255) NOT NULL, password VARCHAR(2000), first_name VARCHAR(255), last_name VARCHAR(255), CONSTRAINT username_pk PRIMARY KEY (username));
CREATE TABLE oauth_scopes (scope TEXT, is_default BOOLEAN);
CREATE TABLE oauth_jwt (client_id VARCHAR(80) NOT NULL, subject VARCHAR(80), public_key VARCHAR(2000), CONSTRAINT jwt_client_id_pk PRIMARY KEY (client_id));
*/
