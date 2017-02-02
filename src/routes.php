<?php
// Routes
$app->post('/auth', OAuth2Server\Actions\AuthCodeAction::class)
    ->setName('auth_code');

$app->post('/token', OAuth2Server\Actions\TokenAction::class)
    ->setName('token');
