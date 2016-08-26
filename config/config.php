<?php

return array(

    'bootstrap' => array(
        'path' => APPLICATION_PATH.DS.'Bootstrap.php',
    ),

    'debug' => getenv('PREVARISC_DEBUG_ENABLED'),

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
                'port' => getenv('PREVARISC_DB_PORT'),
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
        'key' => getenv('PREVARISC_SECURITY_KEY')
    ),

    'phpSettings' => array(
        'display_startup_errors' => getenv('PREVARISC_DEBUG_ENABLED'),
        'display_errors' => getenv('PREVARISC_DEBUG_ENABLED'),
    ),

    'plugins' => getenv('PREVARISC_THIRDPARTY_PLUGINS') ? explode(';', getenv('PREVARISC_THIRDPARTY_PLUGINS')) : [],

    'proxy' => array(
        'enabled' => getenv('PREVARISC_PROXY_ENABLED'),
        'protocol' => getenv('PREVARISC_PROXY_PROTOCOL'),
        'port' => getenv('PREVARISC_PROXY_PORT'),
        'host' => getenv('PREVARISC_PROXY_HOST'),
        'username' => getenv('PREVARISC_PROXY_USERNAME'),
        'password' => getenv('PREVARISC_PROXY_PASSWORD'),
    ),

    'carto' => array(
        'ign' => getenv('PREVARISC_PLUGIN_IGNKEY'),
        'google' => getenv('PREVARISC_PLUGIN_GOOGLEMAPKEY')
    ),

    'types_sans_local_sommeil' => getenv('PREVARISC_LOCAL_SOMMEIL_TYPES') ? explode(';', getenv('PREVARISC_LOCAL_SOMMEIL_TYPES')) : array(7,11),

    'type_commission_communale' => getenv('PREVARISC_COMMISSION_COMMUNALE_TYPE') ? getenv('PREVARISC_COMMISSION_COMMUNALE_TYPE') : 2,

    'dashboard' => array(
        'next_commissions_days' => getenv('PREVARISC_DASHBOARD_NEXT_COMMISSIONS_DAYS'),
        'dossiers_sans_avis_days' => getenv('PREVARISC_DASHBOARD_DOSSIERS_SANS_AVIS_DAYS'),
        'courrier_sans_reponse_days' => getenv('PREVARISC_DASHBOARD_COURRIER_SANS_REPONSE_DAYS')
    ),

    'auth' => array(
        'cas' => array(
            'enabled' => getenv('PREVARISC_CAS_ENABLED'),
            'version' => getenv('PREVARISC_CAS_VERSION'),
            'host' => getenv('PREVARISC_CAS_HOST'),
            'port' => getenv('PREVARISC_CAS_PORT'),
            'context' => getenv('PREVARISC_CAS_CONTEXT'),
            'no_server_validation' => getenv('PREVARISC_CAS_NO_SERVER_VALIDATION'),
        ),
        'ntlm' => array(
            'enabled' => getenv('PREVARISC_NTLM_ENABLED')
        ),
        'ldap' => array(
            'enabled' => getenv('PREVARISC_LDAP_ENABLED'),
            'host' => getenv('PREVARISC_LDAP_ENABLED') ? getenv('PREVARISC_LDAP_HOST') : '',
            'port' => getenv('PREVARISC_LDAP_PORT') ? getenv('PREVARISC_LDAP_PORT') : 389,
            'username' => getenv('PREVARISC_LDAP_ENABLED') ? getenv('PREVARISC_LDAP_USERNAME') : '',
            'password' => getenv('PREVARISC_LDAP_ENABLED') ? getenv('PREVARISC_LDAP_PASSWORD') : '',
            'baseDn' => getenv('PREVARISC_LDAP_ENABLED') ? getenv('PREVARISC_LDAP_BASEDN') : '',
            'account_form' => getenv('PREVARISC_LDAP_ACCOUNT_FORM')
        )
    )

);
