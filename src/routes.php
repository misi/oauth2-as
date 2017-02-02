<?php
// Routes
$app->get('/auth', OAuth2Server\Actions\AuthCodeAction::class);

$app->post('/auth', OAuth2Server\Actions\AuthCodeAction::class);

$app->post('/token', OAuth2Server\Actions\TokenAction::class)
    ->setName('token');
