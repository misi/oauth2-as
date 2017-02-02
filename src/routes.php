<?php
// Routes
$app->get('auth_code', OAuth2Server\Action\AuthCodeAction::class)
    ->setName('auth_code');
