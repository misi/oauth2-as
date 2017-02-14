<?php
// Application middleware
// Session
$app->add(new \Slim\Middleware\Session($container->get('settings')['session']));
