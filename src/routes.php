<?php
// Routes
$app->get('/as/auth', OAuth2Server\Action\AuthCodeAction::class)
    ->setName('auth_code');
