<?php
// Routes
$app->get('/', OAuth2Server\Actions\AuthCodeAction::class)
    ->setName('auth_code');
