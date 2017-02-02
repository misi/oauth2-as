<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
    ],
];
return [
    'settings' => [
        // Slim Settings
        'displayErrorDetails' => true, // set to false in production
        
        // api rate limiter settings
        'api_rate_limiter' => [
            'requests' => '100',
            'inmins' => '60',
        ],
        // database settings
        'pdo' => [
            'dsn' => 'mysql:host=localhost;dbname=as;charset=utf8mb4;collation=utf8mb4_unicode_ci',
            'username' => 'db_user',
            'password' => 'db_password',
        ],
        // Monolog settings
        'logger' => [
            'name' => 'Auth Sever app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
