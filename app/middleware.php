<?php
// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);
$app->add(new \Slim\Csrf\Guard);

// Session
$app->add(new \Slim\Middleware\Session($app->settings['session']));
