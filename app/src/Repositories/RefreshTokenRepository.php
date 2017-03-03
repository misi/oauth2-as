<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2Server\Repositories;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use OAuth2Server\Entities\RefreshTokenEntity;

use Psr\Log\LoggerInterface;
use \PDO;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
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
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity)
    {
        // Some logic to persist the refresh token in a database
        $sql="INSERT INTO refresh_token
                          (refresh_token_id, expiry)
                          VALUES (:refresh_token_id, from_unixtime(:expiry))";
        $stmt=$this->pdo->prepare($sql);

        $stmt->bindParam(':refresh_token_id', $refreshTokenEntity->getIdentifier(), PDO::PARAM_STR);
        $stmt->bindParam(':expiry', $refreshTokenEntity->getExpiryDateTime()->getTimestamp(), PDO::PARAM_INT);

        $stmt->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        // Some logic to revoke the refresh token in a database
        $sql="DELETE FROM refresh_token
                          WHERE refresh_token_id=:refresh_token_id";
        $stmt=$this->pdo->prepare($sql);
        $stmt->bindParam(':refresh_token_id', $tokenId , PDO::PARAM_STR);
        $stmt->execute();

    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        $sql="SELECT COUNT(*) FROM refresh_token
                      WHERE refresh_token_id=:refresh_token_id and expiry >= NOW()";
        $stmt=$this->pdo->prepare($sql);
        $stmt->bindParam(':refresh_token_id', $tokenId , PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->fetchColumn() == 1) {
            return false; // Access token hasn't been revoked
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }
}
