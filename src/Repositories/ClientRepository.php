<?php
/**
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace OAuth2Server\Repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use OAuth2Server\Entities\ClientEntity;

use Psr\Log\LoggerInterface;
use \PDO;

class ClientRepository implements ClientRepositoryInterface
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
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {


        // Check if client is registered

        $sql="SELECT `id`,
                     `name`,
                     `client_secret`,
                     `redirect_uri`,
                     `confidential`
                FROM `client`
                LEFT JOIN `acl` ON `client`.`id` = `acl`.`client_id`
                WHERE `client`.`uuid`=:uuid
                  AND `acl`.`grant_type`=:grant_type";

        // only allow confidential clients to grant client credential
        if ($grantType =='client_credentials') $sql.=" and client.confidential=1";

        $stmt=$this->pdo->prepare($sql);

        $stmt->bindParam(':uuid', $clientIdentifier, PDO::PARAM_STR);
        $stmt->bindParam(':grant_type', $grantType, PDO::PARAM_STR);

        $stmt->execute();

        $data=$stmt->fetch();

        if ( $stmt->rowCount() != 1){
            return;
        }
        $this->logger->info("misi: ".$data['confidential']."\n".$data['client_secret']."\n".password_verify($clientSecret, $data['client_secret']));
        if (
            $mustValidateSecret === true
            && $data['confidential'] == 1
            && password_verify($clientSecret, $data['client_secret']) === false
        ) {
            return;
        }

        $client = new ClientEntity();
        $client->setIdentifier($data['id']);
        $client->setName($data['name']);
        $client->setRedirectUri($data['redirect_uri']);

        return $client;
    }
}
