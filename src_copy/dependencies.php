<?php
use App\Action\OtherAction;
use App\Action\IntezmenyAction;
use App\Action\CimAction;
use App\Action\EmberAction;
use App\Action\EszkozAction;
use App\Action\DropsAction;

use App\Factory\OtherFactory;
use App\Factory\IntezmenyFactory;
use App\Factory\CimFactory;
use App\Factory\EmberFactory;
use App\Factory\EszkozFactory;
use App\Factory\DropsFactory;

use App\Repository\OtherRepository;
use App\Repository\DefaultRepository;
use App\Repository\IntezmenyRepository;
use App\Repository\CimRepository;
use App\Repository\EmberRepository;
use App\Repository\EszkozRepository;
use App\Repository\DropsRepository;

use App\Validation\Validator;

use Slim\Http\Request;

use App\Utils\Token;
use Slim\Middleware\JwtAuthentication;

$container = $app->getContainer();

$container["token"] = function ($container) {
    return new Token;
};

///if($container->get('settings')['iirapi']['JwtAuth']) {
$container["JwtAuthentication"] = function ($container) {
    return new JwtAuthentication([
        "path" => "/",
        "secret" => "abc",
        "secure" => false,
        "passthrough" => ["/v1/swagger", "/v1/docs", "/templates"],
        "error" => function ($request, $response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            ///$data["message"] = "Invalid or expired token";
            return $response
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        },
        "callback" => function ($request, $response, $arguments) use ($container) {
            $container["token"]->hydrate($arguments["decoded"]);
        }
    ]);
};
//}

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG));

    return $logger;
};

// http cache
$container['cache'] = function () {
    return new \Slim\HttpCache\CacheProvider();
};

// php-view
$container['view'] = function ($container) {
    return new \Slim\Views\PhpRenderer('templates/');
};

// error handler
if ($container->get('settings')['IIRAPISettings']['errorHandler']) {
    $container['errorHandler'] = function ($c) {
        return function ($request, $response, $exception) use ($c) {
            return $c['response']->withStatus(500)
                                 ->withHeader('Content-Type', 'application/json')
                                 ->withJson(array('info' => 'Something went wrong!'));
        };
    };
}

// notFound handler
if ($container->get('settings')['IIRAPISettings']['notFoundHandler']) {
    $container['notFoundHandler'] = function ($c) {
        return function ($request, $response) use ($c) {
            return $c['response']
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json')
                ->withJson(array('info' => 'Route not defined'));
        };
    };
}

// IIR API Settings
$container['IIRAPISettings'] = function ($c) {
    return $c->get('settings')['IIRAPISettings'];
};

// Database PDO
$container['pdo'] = function ($c) {
    $settings = $c->get('settings')['pdo'];

    return new \Slim\PDO\Database($settings['dsn'], $settings['username'], $settings['password'], array(PDO::ATTR_FETCH_TABLE_NAMES => true) );
};

// Action
$container[App\Action\OtherAction::class] = function ($c) {
    return new OtherAction($c->get('token'), $c->get('logger'), $c->get(App\Factory\OtherFactory::class), $c->get(App\Repository\OtherRepository::class), $c->get(App\Repository\DefaultRepository::class), $c->get('cache'), $c->get('IIRAPISettings'));
};
$container[App\Action\IntezmenyAction::class] = function ($c) {
    return new IntezmenyAction($c->get('token'), $c->get('logger'), $c->get(App\Factory\IntezmenyFactory::class), $c->get(App\Repository\IntezmenyRepository::class), $c->get(App\Repository\DefaultRepository::class), $c->get('cache'), $c->get('IIRAPISettings'));
};
$container[App\Action\CimAction::class] = function ($c) {
    return new CimAction($c->get('token'), $c->get('logger'), $c->get(App\Factory\CimFactory::class), $c->get(App\Repository\CimRepository::class), $c->get(App\Repository\DefaultRepository::class), $c->get('cache'), $c->get('IIRAPISettings'));
};
$container[App\Action\EmberAction::class] = function ($c) {
    return new EmberAction($c->get('token'), $c->get('logger'), $c->get(App\Factory\EmberFactory::class), $c->get(App\Repository\EmberRepository::class), $c->get(App\Repository\DefaultRepository::class), $c->get('cache'), $c->get('IIRAPISettings'));
};
$container[App\Action\EszkozAction::class] = function ($c) {
    return new EszkozAction($c->get('token'), $c->get('logger'), $c->get(App\Factory\EszkozFactory::class), $c->get(App\Repository\EszkozRepository::class), $c->get(App\Repository\DefaultRepository::class), $c->get('cache'), $c->get('IIRAPISettings'));
};
$container[App\Action\DropsAction::class] = function ($c) {
    return new DropsAction($c->get('token'), $c->get('logger'), $c->get(App\Factory\DropsFactory::class), $c->get(App\Repository\DropsRepository::class), $c->get(App\Repository\DefaultRepository::class), $c->get('cache'), $c->get('IIRAPISettings'));
};

// Factory
$container[App\Factory\OtherFactory::class] = function ($c) {
    return new OtherFactory($c->get('logger'), $c->get('pdo'), $c->get(App\Repository\OtherRepository::class), $c->get(App\Repository\DefaultRepository::class));
};
$container[App\Factory\IntezmenyFactory::class] = function ($c) {
    return new IntezmenyFactory($c->get('logger'), $c->get('pdo'), $c->get(App\Repository\IntezmenyRepository::class), $c->get(App\Repository\DefaultRepository::class));
};
$container[App\Factory\CimFactory::class] = function ($c) {
    return new CimFactory($c->get('logger'), $c->get('pdo'), $c->get(App\Repository\CimRepository::class), $c->get(App\Repository\DefaultRepository::class));
};
$container[App\Factory\EmberFactory::class] = function ($c) {
    return new EmberFactory($c->get('logger'), $c->get('pdo'), $c->get(App\Repository\EmberRepository::class), $c->get(App\Repository\DefaultRepository::class));
};
$container[App\Factory\EszkozFactory::class] = function ($c) {
    return new EszkozFactory($c->get('logger'), $c->get('pdo'), $c->get(App\Repository\EszkozRepository::class), $c->get(App\Repository\DefaultRepository::class));
};
$container[App\Factory\DropsFactory::class] = function ($c) {
    return new DropsFactory($c->get('logger'), $c->get('pdo'), $c->get(App\Repository\DropsRepository::class), $c->get(App\Repository\DefaultRepository::class));
};

// Repository
$container[App\Repository\DefaultRepository::class] = function ($c) {
    return new DefaultRepository($c->get('logger'), $c->get('pdo'));
};
$container[App\Repository\OtherRepository::class] = function ($c) {
    return new OtherRepository($c->get('logger'), $c->get('pdo'), $c->get(App\Repository\DefaultRepository::class));
};
$container[App\Repository\IntezmenyRepository::class] = function ($c) {
    return new IntezmenyRepository($c->get('logger'), $c->get('pdo'), $c->get(App\Repository\DefaultRepository::class), $c->get(App\Repository\EszkozRepository::class));
};
$container[App\Repository\CimRepository::class] = function ($c) {
    return new CimRepository($c->get('logger'), $c->get('pdo'), $c->get(App\Repository\DefaultRepository::class));
};
$container[App\Repository\EmberRepository::class] = function ($c) {
    return new EmberRepository($c->get('logger'), $c->get('pdo'), $c->get(App\Repository\DefaultRepository::class));
};
$container[App\Repository\EszkozRepository::class] = function ($c) {
    return new EszkozRepository($c->get('logger'), $c->get('pdo'), $c->get(App\Repository\DefaultRepository::class));
};
$container[App\Repository\DropsRepository::class] = function ($c) {
    return new DropsRepository($c->get('logger'), $c->get('pdo'), $c->get(App\Repository\DefaultRepository::class));
};


