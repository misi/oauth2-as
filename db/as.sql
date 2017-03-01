CREATE TABLE `client` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `public_id` varchar(255) NOT NULL COMMENT 'Client ID / client_id',
  `name` varchar(255) NOT NULL COMMENT 'Client/App name',
  `description` varchar(6000) NOT NULL COMMENT 'Client/App description',
  `client_secret` varchar(2000) DEFAULT NULL COMMENT 'Encrypted Client Secret',
  `redirect_uri` varchar(2000) DEFAULT NULL COMMENT 'redirect URI',
  `confidential` BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Confidential client (could store secret)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB;

CREATE TABLE `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL COMMENT 'Login name',
  `password` varchar(2000) NULL COMMENT 'Encrypted Password',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `scope` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(1000) NOT NULL COMMENT 'Name of the scope',
  `description` varchar(2000) DEFAULT NULL COMMENT 'Description of the scope',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `acl` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `grant_type` ENUM('authorization_code','client_credentials','password','implicit','refresh_token') NOT NULL COMMENT 'Grant Type',
  PRIMARY KEY (`id`),
  FOREIGN KEY (`client_id`) REFERENCES `client` (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `acl_scope` (
  `acl_id` bigint(20) unsigned NOT NULL,
  `scope_id` bigint(20) unsigned NOT NULL,
  `scope_is_default` BOOLEAN NOT NULL DEFAULT 0 COMMENT 'Force to add it to scopes, even if it is not requested',
  PRIMARY KEY (`acl_id`,`scope_id`),
  FOREIGN KEY (`acl_id`) REFERENCES `acl` (`id`),
  FOREIGN KEY (`scope_id`) REFERENCES `scope` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `auth_code` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `auth_code_id` varchar(1000) NOT NULL COMMENT 'Auth Code ID',
  `expiry` timestamp NULL DEFAULT NULL COMMENT 'Expiry Timestamp',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation Timestamp',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `access_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `token_id` varchar(50) NOT NULL COMMENT 'Token',
  `expiry` timestamp NULL DEFAULT NULL COMMENT 'Expiry Timestamp',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation Timestamp',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `refresh_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `refresh_token_id` varchar(50) NOT NULL COMMENT 'Refresh Token ID',
  `expiry` timestamp NULL DEFAULT NULL COMMENT 'Expiry Timestamp',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Creation Timestamp',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

#select user.username,client.name, scope.name from acl left join client on client.id = acl.client_id left join user on user.id = acl.user_id left join acl_scope on acl.id = acl_scope.acl_id left join scope on acl_scope.scope_id = scope.id;
