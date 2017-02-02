<?php
// Routes
$app->get('/as/auth', OAuth2Server\Actions\AuthCodeAction::class)
    ->setName('auth_code');
