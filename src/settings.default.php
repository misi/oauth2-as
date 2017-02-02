<?php
return [
    'settings' => [
        // Slim Settings
        'displayErrorDetails' => true, // set to false in production

        // database settings
        'pdo' => [
            'dsn' => 'mysql:host=localhost;dbname=as;charset=utf8mb4;collation=utf8mb4_unicode_ci',
            'username' => 'db_user',
            'password' => 'db_password',
        ],

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'Auth Sever app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
