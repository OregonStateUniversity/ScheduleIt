<?php

require_once dirname(__FILE__) . '/scheduleit.config.php';

return
    [
        'paths' => [
            'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        ],
        'environments' =>
            [
                'default_database' => 'production',
                'default_migration_table' => 'meb_migrations',
                'production' =>
                    [
                        'adapter' => 'mysql',
                        'host' => $_ENV['DATABASE_HOST_ADMIN'],
                        'name' => $_ENV['DATABASE_NAME_ADMIN'],
                        'user' => $_ENV['DATABASE_USERNAME_ADMIN'],
                        'pass' => $_ENV['DATABASE_PASSWORD_ADMIN'],
                        'port' => $_ENV['DATABASE_PORT_ADMIN'],
                        'charset' => 'utf8mb4',
                        'collation' => 'utf8mb4_general_ci',
                    ],
            ],
    ];
