<?php
namespace OAuth2Server\Actions;


use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use OAuth2Server\Entities\UserEntity;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Stream;

use Psr\Log\LoggerInterface;

final class TokenAction
{
    private $logger;

    private $authserver;

    public function __construct(AuthorizationServer $authserver, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->authserver = $authserver;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->logger->info("token action dispatched");
        print_r($authserver);

          try{
            // Try to respond to the access token request
            return $this->authserver->respondToAccessTokenRequest($request, $response);

          } catch (OAuthServerException $exception) {

            // All instances of OAuthServerException can be converted to a PSR-7 response
            return $exception->generateHttpResponse($response);
          } catch (\Exception $exception) {

            // Catch unexpected exceptions
            $body = $response->getBody();
            $body->write($exception->getMessage());

            return $response->withStatus(500)->withBody($body);
          }

    }
}
