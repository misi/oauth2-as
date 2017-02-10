<?php
return [
    'settings' => [
        // Slim Settings
        'displayErrorDetails' => true, // set to false in production

        // View settings
        'view' => [
            'template_path' => __DIR__ . '/templates',
            'twig' => [
                'cache' => __DIR__ . '/../cache/twig',
                'debug' => true,
                'auto_reload' => true,
            ],
        ],

        // database settings
        'pdo' => [
            'dsn' => 'mysql:host=localhost;dbname=as;charset=utf8mb4;collation=utf8mb4_unicode_ci',
            'username' => 'db_user',
            'password' => 'db_password',
            'options'=> [ PDO::ATTR_FETCH_TABLE_NAMES => 'true',
                          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                        ],
        ],

        // Monolog settings
        'logger' => [
            'name' => 'Auth Sever app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
