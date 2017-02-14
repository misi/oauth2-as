<?php
// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);
$app->add(new \Slim\Csrf\Guard);

// Session
$settings=$app->get('settings')['session']);
$app->add(new \Slim\Middleware\Session([
  'name' => 'dummy_session',
  'autorefresh' => true,
  'lifetime' => '1 hour'
]);
