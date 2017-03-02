<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2Server\Repositories;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use OAuth2Server\Entities\AccessTokenEntity;

use Psr\Log\LoggerInterface;
use \PDO;

class AccessTokenRepository implements AccessTokenRepositoryInterface
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
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        // Some logic here to save the access token to a database
        $sql="INSERT INTO access_token
                          (access_token_id, expiry)
                          VALUES (:access_token_id, from_unixtime(:expiry))";
        $stmt=$this->pdo->prepare($sql);

        $stmt->bindParam(':access_token_id', $accessTokenEntity->getIdentifier(), PDO::PARAM_STR);
        $stmt->bindParam(':expiry', $accessTokenEntity->getExpiryDateTime()->getTimestamp(), PDO::PARAM_INT);

        $stmt->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeAccessToken($tokenId)
    {
        // Some logic here to revoke the access token
        $sql="DELETE FROM access_token
                          WHERE access_token_id=:access_token_id";
        $stmt=$this->pdo->prepare($sql);
        $stmt->bindParam(':access_token_id', $tokenId , PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        $sql="SELECT id FROM access_token
                        WHERE access_token_id=:access_token_id  and expiry >= NOW()";
        $stmt=$this->pdo->prepare($sql);
        $stmt->bindParam(':access_token_id', $tokenId , PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->fetchColumn() == 1) {
            return false; // Access token hasn't been revoked
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessTokenEntity();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }
}
