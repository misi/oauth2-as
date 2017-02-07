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
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use OAuth2Server\Entities\ScopeEntity;

use Psr\Log\LoggerInterface;
use \PDO;

class ScopeRepository implements ScopeRepositoryInterface
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
    public function getScopeEntityByIdentifier($scopeIdentifier)
    {
      $sql="SELECT `id` FROM `scope`
                WHERE `name` = :scope";

      $stmt=$this->pdo->prepare($sql);
      $stmt->bindParam(':scope', $scopeIdentifier, PDO::PARAM_STR);
      $stmt->execute();

      $data=$stmt->fetch();

      if ( $stmt->rowCount() != 1 ){
          return;
      }

      $scope = new ScopeEntity();
      $scope->setIdentifier($data['id']);

      return $scope;
    }

    /**
     * {@inheritdoc}
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
      //userIdentifier=null is only valid if grantType is client_credentials
      if (!isset($userIdentifier) && $grantType!='client_credentials') {
        return;
      }

      $sql="SELECT `scope`.`id`,
                   `acl_scope`.`scope_is_default`
                FROM `acl`
                LEFT JOIN `client` ON `client`.`id` = `acl`.`client_id`";

      if ($userIdentifier) {
           $sql.="    LEFT JOIN `user` ON `user`.`id` = `acl`.`user_id`";
      };

      $sql.=" LEFT JOIN `client` ON `client`.`id` = `acl`.`client_id`
             LEFT JOIN `acl_scope` ON `acl`.`id` = `acl_scope`.`acl_id`
             LEFT JOIN `scope` ON `acl_scope`.`scope_id` = `scope`.`id`
            WHERE `acl`.`grant_type` = :grant_type
              AND `client`.`id` = :client_id";

      if ($userIdentifier) {
         $sql.=" AND `user`.`id` = :user_id";
      }

      $this->logger->info("sql: ".$sql."\nclient_id: ".$clientEntity->getIdentifier()."\ngrant: ".$grantType."\nuserid: ".$userIdentifier);

      $stmt=$this->pdo->prepare($sql);

      $stmt->bindParam(':grant_type', $grantType, PDO::PARAM_STR);
      $stmt->bindParam(':client_id', $clientEntity->getIdentifier(), PDO::PARAM_INT);

      if ($userIdentifier) {
        $stmt->bindParam(':user_id', $userIdentifier, PDO::PARAM_INT);
      }

      $stmt->execute();

      $result=$stmt->fetchAll();

      foreach ($result as $row) {
        if ($row['scope_is_default']) {
          $defaultscope = new ScopeEntity();
          $defaultscope->setIdentifier($row['id']);
          $scopes[]=$defaultscope;
          continue;
        }
        foreach ($sopes as $scope) {
          if ($row['id'] === $scope->getIdentifier()) {
            $scopes[]=$scope;
          }
        }
      }

      return $scopes;
    }
}
