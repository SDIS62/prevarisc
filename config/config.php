<?php

return array(

    'bootstrap' => array(
        'path' => APPLICATION_PATH.DS.'Bootstrap.php',
    ),

    'resources' => array(

        'frontController' => array(
            'controllerDirectory' => APPLICATION_PATH.DS.'controllers',
            'moduleDirectory' => APPLICATION_PATH.DS.'modules',
            'params' => array(
                'displayExceptions' => getenv('PREVARISC_DEBUG_ENABLED'),
            ),
        ),

        'db' => array(
            'adapter' => getenv('PREVARISC_DB_ADAPTER'),
            'params' => array(
                'host' => getenv('PREVARISC_DB_HOST'),
                'charset' => getenv('PREVARISC_DB_CHARSET'),
                'username' => getenv('PREVARISC_DB_USERNAME'),
                'password' => getenv('PREVARISC_DB_PASSWORD'),
                'dbname' => getenv('PREVARISC_DB_DBNAME'),
            ),
        ),

        'dataStore' => array(
            'adapter' => getenv('PREVARISC_DATASTORE_ADAPTER') ? getenv('PREVARISC_DATASTORE_ADAPTER') : 'Plugin_SimpleFileDataStore',
        ),

        'modules' => '',
        
    ),

    'ldap' => array(
        'enabled' => getenv('PREVARISC_LDAP_ENABLED'),
        'host' => getenv('PREVARISC_LDAP_ENABLED') ? getenv('PREVARISC_LDAP_HOST') : '',
        'username' => getenv('PREVARISC_LDAP_ENABLED') ? getenv('PREVARISC_LDAP_USERNAME') : '',
        'password' => getenv('PREVARISC_LDAP_ENABLED') ? getenv('PREVARISC_LDAP_PASSWORD') : '',
        'baseDn' => getenv('PREVARISC_LDAP_ENABLED') ? getenv('PREVARISC_LDAP_BASEDN') : '',
    ),

    'cache' => array(
        'adapter'       => getenv('PREVARISC_CACHE_ADAPTER') ? : 'File',
        'customAdapter' => getenv('PREVARISC_CACHE_ADAPTER') !== false,
        'enabled'      => ((int) getenv('PREVARISC_CACHE_LIFETIME')) > 0,
        'lifetime'      => (int) getenv('PREVARISC_CACHE_LIFETIME'),
        'host'          => getenv('PREVARISC_CACHE_HOST'),
        'port'          => (int) getenv('PREVARISC_CACHE_PORT'),
        'write_control' => false,
        'compression'   => false,
        'cache_dir'     => getenv('PREVARISC_CACHE_DIR') ? : APPLICATION_PATH.DS.'..'.DS.'cache',
        'read_control'  => false,
    ),

    'security' => array(
        'salt' => getenv('PREVARISC_SECURITY_SALT'),
    ),

    'phpSettings' => array(
        'display_startup_errors' => getenv('PREVARISC_DEBUG_ENABLED'),
        'display_errors' => getenv('PREVARISC_DEBUG_ENABLED'),
    )

);
