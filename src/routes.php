<?php
// Routes
$app->get('/auth', OAuth2Server\Actions\AuthCodeAction::class)
    ->setName('auth_code');
