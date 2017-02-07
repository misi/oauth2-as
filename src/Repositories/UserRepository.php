<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2Server\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use OAuth2Server\Entities\UserEntity;

use Psr\Log\LoggerInterface;
use \PDO;

class UserRepository implements UserRepositoryInterface
{
    private $logger;

    private $pdo;

    public function __construct(PDO $pdo, LoggerInterface $logger)
    {
      $this->pdo = $pdo;
      $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserEntityByUserCredentials(
        $username,
        $password,
        $grantType,
        ClientEntityInterface $clientEntity
    ) {

        $sql='SELECT `user`.`id` AS `id`,
                     `user`.`password` AS `password`
                  FROM `acl`
                  LEFT JOIN `client` ON `client`.`id` = `acl`.`client_id`
                  LEFT JOIN `user` ON `user`.`id` = `acl`.`user_id`
                  WHERE `user`.`username` = :username
                   AND  `client`.`id` = :client_id';

        $stmt=$this->pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':client_id', $clientEntity->getIdentifier(), PDO::PARAM_STR);
        $stmt->execute();

        $data=$stmt->fetch();

        $this->logger->info("client_id:".$clientEntity->getIdentifier());

        $this->logger->info("password:".$data['password']);
        $this->logger->info("rowcount:".$stmt->rowCount());

        if ( $stmt->rowCount() != 1 ){
            return;
        }

        $this->logger->info("password:".$data['password']);

        if( password_verify($password, $data['password']) === true ) {
          $user = new UserEntity();
          $user->setIdentifier($data['id']);
          return $user;
        }

        return;
    }
}
