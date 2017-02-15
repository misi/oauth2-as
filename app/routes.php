<?php
// Routes

//GET
$app->get('/authorize', OAuth2Server\Actions\AuthCodeAction::class);

//POST
$app->post('/authorize', OAuth2Server\Actions\AuthCodeAction::class);

$app->post('/access_token', OAuth2Server\Actions\TokenAction::class)
    ->setName('token');
