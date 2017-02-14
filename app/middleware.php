<?php
// Application middleware
// Session
$app->add(new \Slim\Middleware\Session($container->get('settings')['session']));

// e.g: $app->add(new \Slim\Csrf\Guard);
$app->add(new \Slim\Csrf\Guard);
