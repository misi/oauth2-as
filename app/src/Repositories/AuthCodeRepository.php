<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2Server\Repositories;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use OAuth2Server\Entities\AuthCodeEntity;

use Psr\Log\LoggerInterface;
use \PDO;

class AuthCodeRepository implements AuthCodeRepositoryInterface
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
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity)
    {
        // Some logic to persist the auth code to a database
        $sql="INSERT INTO auth_code
                          (auth_code_id, expiry)
                          VALUES (:auth_code_id, from_unixtime(:expiry))";
        $stmt=$this->pdo->prepare($sql);

        $stmt->bindParam(':auth_code_id', $authCodeEntity->getIdentifier(), PDO::PARAM_STR);
        $stmt->bindParam(':expiry', $authCodeEntity->getExpiryDateTime()->getTimestamp(), PDO::PARAM_STR);

        $stmt->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAuthCode($codeId)
    {
        // Some logic to revoke the auth code in a database
        $sql="DELETE FROM auth_code
                          WHERE auth_code_id=:auth_code_id";
        $stmt=$this->pdo->prepare($sql);
        $stmt->bindParam(':auth_code_id', $codeId , PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthCodeRevoked($codeId)
    {
      $sql="SELECT COUNT(*) FROM auth_code
                      WHERE auth_code_id=:auth_code_id and expiry >= NOW()";
      $stmt=$this->pdo->prepare($sql);
      $stmt->bindParam(':auth_code_id', $codeId , PDO::PARAM_STR);
      $stmt->execute();
      if ($stmt->fetchColumn() == 1) {
          return false; // Access token hasn't been revoked
      }
      return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }
}
