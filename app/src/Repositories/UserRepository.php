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

use Interop\Container\ContainerInterface;

use \\PDO

class UserRepository implements UserRepositoryInterface
{
    private $logger;

    private PDO $pdo;

    public function __construct(ContainerInterface $c)
    {
      $this->logger = $c->get('logger');
      $this->pdo = $c->get('pdo');
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
                   AND  `client`.`id` = :client_id
                   AND `acl`.`grant_type` = :grant_type';

        $stmt=$this->pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':client_id', $clientEntity->getInternalID(), PDO::PARAM_STR);
        $stmt->bindParam(':grant_type', $grantType, PDO::PARAM_STR);
        $stmt->execute();

        $data=$stmt->fetch();

        if ( $stmt->rowCount() != 1 ){
            return;
        }

        if( password_verify($password, $data['password']) === true ) {
          $user = new UserEntity();
          $user->setIdentifier($data['id']);
          return $user;
        }

        return;
    }
}
