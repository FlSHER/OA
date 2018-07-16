<?php

return [

    /*
      |--------------------------------------------------------------------------
      | PDO Fetch Style
      |--------------------------------------------------------------------------
      |
      | By default, database results will be returned as instances of the PHP
      | stdClass object; however, you may desire to retrieve records in an
      | array format for simplicity. Here you can tweak the fetch style.
      |
     */

    'fetch' => PDO::FETCH_OBJ,
    /*
      |--------------------------------------------------------------------------
      | Default Database Connection Name
      |--------------------------------------------------------------------------
      |
      | Here you may specify which of the database connections below you wish
      | to use as your default connection for all database work. Of course
      | you may use many connections at once using the Database library.
      |
     */
    'default' => env('DB_CONNECTION', 'mysql'),
    /*
      |--------------------------------------------------------------------------
      | Database Connections
      |--------------------------------------------------------------------------
      |
      | Here are each of the database connections setup for your application.
      | Of course, examples of configuring each database platform that is
      | supported by Laravel is shown below to make development simple.
      |
      |
      | All database work in Laravel is done through the PHP PDO facilities
      | so make sure you have the driver for your particular database of
      | choice installed on your machine before you begin development.
      |
     */
    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        'mysql_online' => [
            'driver' => 'mysql',
            'host' => env('MYSQLONLINE_DB_HOST', 'localhost'),
            'port' => env('MYSQLONLINE_DB_PORT', '3306'),
            'database' => env('MYSQLONLINE_DB_DATABASE', 'forge'),
            'username' => env('MYSQLONLINE_DB_USERNAME', 'forge'),
            'password' => env('MYSQLONLINE_DB_PASSWORD', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', 'localhost'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
        //报销
        'reimburse_mysql' => [
            'driver' => 'mysql',
            'host' => env('REIMBURSE_DB_HOST', 'localhost'),
            'port' => env('REIMBURSE_DB_PORT', '3306'),
            'database' => env('REIMBURSE_DB_DATABASE', 'forge'),
            'username' => env('REIMBURSE_DB_USERNAME', 'forge'),
            'password' => env('REIMBURSE_DB_PASSWORD', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => 'bx_',
            'strict' => true,
            'engine' => null,
        ],
        //考勤
        'attendance' => [
            'driver' => 'mysql',
            'host' => env('ATTENDANCE_DB_HOST', 'localhost'),
            'port' => env('ATTENDANCE_DB_PORT', '3306'),
            'database' => env('ATTENDANCE_DB_DATABASE', 'forge'),
            'username' => env('ATTENDANCE_DB_USERNAME', 'forge'),
            'password' => env('ATTENDANCE_DB_PASSWORD', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
//        'attendance' => [
//            'driver' => 'mysql',
//            'host' => '192.168.1.117',
//            'port' => '3306',
//            'database' => 'attendance',
//            'username' => 'root',
//            'password' => 'root',
//            'charset' => 'utf8',
//            'collation' => 'utf8_general_ci',
//            'prefix' => '',
//            'strict' => true,
//            'engine' => null,
//        ],
        //215服务器
        'TDOA' => [
            'driver' => 'mysql',
            'host' => env('TDOA_DB_HOST', 'localhost'),
            'port' => env('TDOA_DB_PORT', '3306'),
            'database' => env('TDOA_DB_DATABASE', 'forge'),
            'username' => env('TDOA_DB_USERNAME', 'forge'),
            'password' => env('TDOA_DB_PASSWORD', ''),
            'charset' => 'gbk',
            'collation' => 'gbk_chinese_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
        //工作任务
        'work_mission' => [
            'driver' => 'mysql',
            'host' => env('WORKMISSION_DB_HOST', 'localhost'),
            'port' => env('WORKMISSION_DB_PORT', '3306'),
            'database' => env('WORKMISSION_DB_DATABASE', 'forge'),
            'username' => env('WORKMISSION_DB_USERNAME', 'forge'),
            'password' => env('WORKMISSION_DB_PASSWORD', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
    ],
    /*
      |--------------------------------------------------------------------------
      | Migration Repository Table
      |--------------------------------------------------------------------------
      |
      | This table keeps track of all the migrations that have already run for
      | your application. Using this information, we can determine which of
      | the migrations on disk haven't actually been run in the database.
      |
     */
    'migrations' => 'migrations',
    /*
      |--------------------------------------------------------------------------
      | Redis Databases
      |--------------------------------------------------------------------------
      |
      | Redis is an open source, fast, and advanced key-value store that also
      | provides a richer set of commands than a typical key-value systems
      | such as APC or Memcached. Laravel makes it easy to dig right in.
      |
     */
    'redis' => [

        'cluster' => false,
        'default' => [
            'host' => env('REDIS_HOST', 'localhost'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],
    ],
];
