<?php

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\PasswordGrant;
use OAuth2ServerMisi\Repositories\AccessTokenRepository;
use OAuth2ServerMisi\Repositories\ClientRepository;
use OAuth2ServerMisi\Repositories\RefreshTokenRepository;
use OAuth2ServerMisi\Repositories\ScopeRepository;
use OAuth2ServerMisi\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;

include __DIR__ . '/../vendor/autoload.php';

$app = new App([
    // Add the authorization server to the DI container
    AuthorizationServer::class => function () {

        // Setup the authorization server
        $server = new AuthorizationServer(
            new ClientRepository(),                 // instance of ClientRepositoryInterface
            new AccessTokenRepository(),            // instance of AccessTokenRepositoryInterface
            new ScopeRepository(),                  // instance of ScopeRepositoryInterface
            'file://' . __DIR__ . '/../private.key',    // path to private key
            'file://' . __DIR__ . '/../public.key'      // path to public key
        );

        $grant = new PasswordGrant(
            new UserRepository(),           // instance of UserRepositoryInterface
            new RefreshTokenRepository()    // instance of RefreshTokenRepositoryInterface
        );
        $grant->setRefreshTokenTTL(new \DateInterval('P1M')); // refresh tokens will expire after 1 month

        // Enable the password grant on the server with a token TTL of 1 hour
        $server->enableGrantType(
            $grant,
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );

        return $server;
    },
]);

$app->post(
    '/access_token',
    function (ServerRequestInterface $request, ResponseInterface $response) use ($app) {

        /* @var \League\OAuth2\Server\AuthorizationServer $server */
        $server = $app->getContainer()->get(AuthorizationServer::class);

        try {

            // Try to respond to the access token request
            return $server->respondToAccessTokenRequest($request, $response);
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
);

$app->run();