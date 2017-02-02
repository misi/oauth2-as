<?php
// Routes
$app->get('/auth', OAuth2Server\Actions\AuthCodeAction::class)
    ->setName('auth_code');

$app->get('/token', OAuth2Server\Actions\TokenAction::class)
    ->setName('token');
