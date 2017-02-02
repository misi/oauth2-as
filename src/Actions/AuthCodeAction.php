<?php
namespace OAuth2Server\Actions\Action;


use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use OAuth2Server\Entities\UserEntity;
use OAuth2Server\Repositories\AccessTokenRepository;
use OAuth2Server\Repositories\AuthCodeRepository;
use OAuth2Server\Repositories\ClientRepository;
use OAuth2Server\Repositories\RefreshTokenRepository;
use OAuth2Server\Repositories\ScopeRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Zend\Diactoros\Stream;

use Psr\Log\LoggerInterface;

final class AuthCodeAction
{
    private $logger;

    private $authserver;

    public function __construct(AuthorizationServer::class $authserver, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->authserver = $authserver;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $this->logger->info("Home page action dispatched");

        return $response;
    }
}
