<?php
/**
 * Database Configuration File
 * This file contains database settings for different environments
 */

return [
    'production' => [
        'host' => 'localhost',
        'username' => 'u722035022_mkscholars',
        'password' => 'Mkscholars123@',
        'database' => 'u722035022_mkscholars',
        'port' => 3306,
        'charset' => 'utf8mb4',
        'options' => [
            MYSQLI_OPT_CONNECT_TIMEOUT => 5,
            MYSQLI_OPT_READ_TIMEOUT => 5,
        ]
    ],
    'local' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'mkscholars',
        'port' => 3306,
        'charset' => 'utf8mb4',
        'options' => [
            MYSQLI_OPT_CONNECT_TIMEOUT => 5,
            MYSQLI_OPT_READ_TIMEOUT => 5,
        ]
    ],
    'staging' => [
        'host' => 'localhost',
        'username' => 'staging_user',
        'password' => 'staging_password',
        'database' => 'mkscholars_staging',
        'port' => 3306,
        'charset' => 'utf8mb4',
        'options' => [
            MYSQLI_OPT_CONNECT_TIMEOUT => 5,
            MYSQLI_OPT_READ_TIMEOUT => 5,
        ]
    ]
];
