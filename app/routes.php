<?php
// Routes

// authserver settings
$settings = $container->get('settings')['authserver'];

//GET
$app->get($settings['authorize_url'], OAuth2Server\Actions\AuthCodeAction::class);

$app->get($settings['redirect_url'], OAuth2Server\Actions\RedirectAction::class);

//POST
$app->post($settings['authorize_url'], OAuth2Server\Actions\AuthCodeAction::class);

$app->post($settings['access_token_url'], OAuth2Server\Actions\TokenAction::class)
    ->setName('token');
