CREATE TABLE `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(255) NOT NULL COMMENT 'Client UUID',
  `name` varchar(255) NOT NULL COMMENT 'Client/App name',
  `client_secret` varchar(2000) DEFAULT NULL COMMENT 'Encrypted Client Secret',
  `redirect_urls` varchar(2000) DEFAULT NULL COMMENT 'redirect URI or a serialiazed indexed array of redirect URIs',
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_id` (`client_id`)
) ENGINE=InnoDB;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) NOT NULL COMMENT 'User UUID',
  `username` varchar(255) NOT NULL COMMENT 'Login name',
  `password` varchar(2000) NOT NULL COMMENT 'Encrypted Password',
  `redirect_urls` varchar(2000) DEFAULT NULL COMMENT 'redirect URI or a serialiazed indexed array of redirect URIs',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `scopes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(1000) NOT NULL COMMENT 'Scope',
  `description` varchar(2000) DEFAULT NULL COMMENT 'Description of the scope',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `relations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `clients_id` bigint(20) unsigned NOT NULL,
  `users_id` bigint(20) unsigned NOT NULL,
  `scopes_id` bigint(20) unsigned NOT NULL,
  `scope_is_default` BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Force to add it to scopes, even if it is not requested',
  `grant_type` ENUM('authorization_code','client_credentials','password','implicit') NOT NULL COMMENT 'Grant Type',
  `token_type` ENUM('Bearer','pop','jwt') NOT NULL DEFAULT 'Bearer' COMMENT 'Token Type',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`clients_id`) REFERENCES `clients` (`id`),
  FOREIGN KEY (`users_id`) REFERENCES `users` (`id`),
  FOREIGN KEY (`scopes_id`) REFERENCES `scopes` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `auth_code` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `relations_id` bigint(20) unsigned NOT NULL,
  `token` varchar(1000) NOT NULL COMMENT 'Token',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation Timestamp',
  `revoked` timestamp NULL DEFAULT NULL COMMENT 'Revocation Timestamp',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`relations_id`) REFERENCES `relations` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `access_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `relations_id` bigint(20) unsigned NOT NULL,
  `token` varchar(1000) NOT NULL COMMENT 'Token',
  `created` timestamp NOT NULL DEFAULT  COMMENT 'Creation Timestamp',
  `revoked` timestamp NULL DEFAULT NULL COMMENT 'Revocation Timestamp',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`relations_id`) REFERENCES `relations` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `refresh_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `relations_id` bigint(20) unsigned NOT NULL,
  `token` varchar(1000) NOT NULL COMMENT 'Token',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation Timestamp',
  `revoked` timestamp NULL DEFAULT NULL COMMENT 'Revocation Timestamp',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`relations_id`) REFERENCES `relations` (`id`)
) ENGINE=InnoDB;
