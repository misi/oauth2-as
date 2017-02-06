CREATE TABLE `client` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL COMMENT 'Client UUID',
  `name` varchar(255) NOT NULL COMMENT 'Client/App name',
  `client_secret` varchar(2000) DEFAULT NULL COMMENT 'Encrypted Client Secret',
  `redirect_urls` varchar(2000) DEFAULT NULL COMMENT 'redirect URI or a serialiazed indexed array of redirect URIs',
  `trusted` BOOLEAN NOT NULL DEFAULT 'false' COMMENT 'redirect URI or a serialiazed indexed array of redirect URIs',
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_id` (`client_id`)
) ENGINE=InnoDB;

CREATE TABLE `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL COMMENT 'Login name',
  `password` varchar(2000) NOT NULL COMMENT 'Encrypted Password',
  `redirect_urls` varchar(2000) DEFAULT NULL COMMENT 'redirect URI or a serialiazed indexed array of redirect URIs',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `scope` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(1000) NOT NULL COMMENT 'Scope',
  `description` varchar(2000) DEFAULT NULL COMMENT 'Description of the scope',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `acl_scope` (
  `acl_id` bigint(20) unsigned NOT NULL,
  `scope_id` bigint(20) unsigned NOT NULL,
  `scope_is_default` BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Force to add it to scopes, even if it is not requested',
  PRIMARY KEY (`acl_id`,`scope_id`),
  FOREIGN KEY (`acl_id`) REFERENCES `acl` (`id`),
  FOREIGN KEY (`scope_id`) REFERENCES `scope` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `acl` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `grant_type` ENUM('authorization_code','client_credentials','password','implicit') NOT NULL COMMENT 'Grant Type',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
) ENGINE=InnoDB;

CREATE TABLE `auth_code` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `code` varchar(1000) NOT NULL COMMENT 'Auth Code',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation Timestamp',
  `revoked` timestamp NULL DEFAULT NULL COMMENT 'Revocation Timestamp',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
) ENGINE=InnoDB;

CREATE TABLE `auth_codes_scopes` (
  `access_token_id` bigint(20) unsigned NOT NULL,
  `scopes_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`access_token_id`,`scopes_id`),
  FOREIGN KEY (`access_token_id`) REFERENCES `access_token` (`id`),
  FOREIGN KEY (`scope_id`) REFERENCES `scope` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `access_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `token` varchar(1000) NOT NULL COMMENT 'Token',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation Timestamp',
  `revoked` timestamp NULL DEFAULT NULL COMMENT 'Revocation Timestamp',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
) ENGINE=InnoDB;

CREATE TABLE `access_token_scope` (
  `access_token_id` bigint(20) unsigned NOT NULL,
  `scopes_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`access_token_id`,`scopes_id`),
  FOREIGN KEY (`access_token_id`) REFERENCES `access_token` (`id`),
  FOREIGN KEY (`scopes_id`) REFERENCES `scopes` (`id`)
) ENGINE=InnoDB;


CREATE TABLE `refresh_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `access_token_id` bigint(20) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation Timestamp',
  `revoked` timestamp NULL DEFAULT NULL COMMENT 'Revocation Timestamp',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  FOREIGN KEY (`access_token_id`) REFERENCES `access_token` (`id`)
) ENGINE=InnoDB;
