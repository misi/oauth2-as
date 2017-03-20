<?php
use OAuth2Server\Repositories\AccessTokenRepository;
use OAuth2Server\Repositories\AuthCodeRepository;
use OAuth2Server\Repositories\UserRepository;
use OAuth2Server\Repositories\ClientRepository;
use OAuth2Server\Repositories\RefreshTokenRepository;
use OAuth2Server\Repositories\ScopeRepository;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\ImplicitGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use Slim\Views\Twig;



// DIC configuration
$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------


// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new Twig($settings['view']['template_path'], $settings['view']['twig']);
    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());
    return $view;
};

// session
$container['session'] = function ($c) {
    return new SlimSession\Helper;
};

//pdo
$container['pdo'] = function ($c) {
    $settings = $c->get('settings')['pdo'];
    $pdo = new \PDO($settings['dsn'], $settings['username'], $settings['password'], $settings['options']);
    return $pdo;
};


// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// UserRepository
$container['userrepository'] = function ($c) {
      return new UserRepository($c->get('pdo'), $c->get('logger'));
};

// error handler
if ($container->get('settings')['authserver']['errorHandler']) {
    $container['errorHandler'] = function ($c) {
        return function ($request, $response, $exception) use ($c) {
            return $c['response']->withStatus(500)
                                 ->withHeader('Content-Type', 'application/json')
                                 ->withJson(array('info' => 'Something went wrong!'));
        };
    };
}

// notFound handler
if ($container->get('settings')['authserver']['notFoundHandler']) {
    $container['notFoundHandler'] = function ($c) {
        return function ($request, $response) use ($c) {
            return $c['response']
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json')
                ->withJson(array('info' => 'Route not defined'));
        };
    };
}

$container['authserver'] = function ($c) {
    $settings = $c->get('settings')['authserver'];

    // Init our repositories
    $userRepository = $c->get('userrepository');
    $clientRepository = new ClientRepository($c->get('pdo'), $c->get('logger'));
    $scopeRepository = new ScopeRepository($c->get('pdo'), $c->get('logger'));
    $authCodeRepository = new AuthCodeRepository($c->get('pdo'), $c->get('logger'));
    $accessTokenRepository = new AccessTokenRepository($c->get('pdo'), $c->get('logger'));
    $refreshTokenRepository = new RefreshTokenRepository($c->get('pdo'), $c->get('logger'));

    $privateKeyPath = 'file://' . __DIR__ . $settings['private_key'];
    $publicKeyPath = 'file://' . __DIR__ . $settings['public_key'];

    // Setup the authorization server
    $server = new AuthorizationServer(
        $clientRepository,
        $accessTokenRepository,
        $scopeRepository,
        $privateKeyPath,
        $publicKeyPath
    );

    // AuthCodeGrant
    if( isset($settings['AuthCodeGrant']) &&
              $settings['AuthCodeGrant']['enabled'] ){
        // Enable the authentication code grant on the server
        $server->enableGrantType(
            new AuthCodeGrant(
                $authCodeRepository,
                $refreshTokenRepository,
                new \DateInterval($settings['AuthCodeGrant']['refresh_token_ttl'])
            ),
            new \DateInterval($settings['AuthCodeGrant']['access_token_ttl'])
        );
    }

    // ClientCredentialsGrant
    if( isset($settings['ClientCredentialsGrant']) &&
              $settings['ClientCredentialsGrant']['enabled'] ){
        // Enable the client credentials grant on the server
        $server->enableGrantType(
            new ClientCredentialsGrant(),
            new \DateInterval($settings['ClientCredentialsGrant']['access_token_ttl'])
        );
    }

    // ImplicitGrant
    if( isset($settings['ImplicitGrant']) &&
              $settings['ImplicitGrant']['enabled'] ){
        // Enable the implicit grant on the server with a token 1TTL of 1 hour
        $server->enableGrantType(
            new ImplicitGrant(
                new \DateInterval($settings['ImplicitGrant']['access_token_ttl'])
            )
        );
    }

    // PasswordGrant
    if( isset($settings['PasswordGrant']) &&
              $settings['PasswordGrant']['enabled'] ){
        // Enable the password grant on the server with a token TTL of 1 hour
        $server->enableGrantType(
            new PasswordGrant(
                $userRepository, // instance of UserRepositoryInterface
                $refreshTokenRepository, // instance of RefreshTokenRepositoryInterface
                // refresh token
                new \DateInterval($settings['PasswordGrant']['refresh_token_ttl'])
        ),
            new \DateInterval($settings['PasswordGrant']['access_token_ttl']) // access token
        );
    }

    // RefreshTokenGrant
    if( isset($settings['RefreshTokenGrant']) &&
              $settings['RefreshTokenGrant']['enabled'] ){
        // Enable the refresh token grant on the server with a token TTL of 1 month
        $server->enableGrantType(
            new RefreshTokenGrant($refreshTokenRepository),
            new \DateInterval('PT1M')
        );
    }

    return $server;
};

// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------
$container[OAuth2Server\Actions\AuthCodeAction::class] = function ($c) {
    return new OAuth2Server\Actions\AuthCodeAction();
};


$container[OAuth2Server\Actions\TokenAction::class] = function ($c) {
    return new OAuth2Server\Actions\TokenAction();
};

$container[OAuth2Server\Actions\RedirectAction::class] = function ($c) {
    return new OAuth2Server\Actions\RedirectAction();
};

//Repositories
$container[OAuth2Server\Repositories\AccessTokenRepository::class] = function ($c) {
    return new OAuth2Server\Repositories\AccessTokenRepository();
};

$container[OAuth2Server\Repositories\AuthCodeRepository::class] = function ($c) {
    return new OAuth2Server\Repositories\AuthCodeRepository();
};

$container[OAuth2Server\Repositories\ClientRepository::class] = function ($c) {
    return new OAuth2Server\Repositories\ClientRepository();
};

$container[OAuth2Server\Repositories\RefreshTokenRepository::class] = function ($c) {
    return new OAuth2Server\Repositories\RefreshTokenRepository();
};

$container[OAuth2Server\Repositories\ScopeRepository::class] = function ($c) {
    return new OAuth2Server\Repositories\ScopeRepository();
};

$container[OAuth2Server\Repositories\UserRepository::class] = function ($c) {
    return new OAuth2Server\Repositories\UserRepository();
};
