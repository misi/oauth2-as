<?php
// Routes
$app->get('as/auth_code', OAuth2Server\Action\AuthCodeAction::class)
    ->setName('auth_code');
