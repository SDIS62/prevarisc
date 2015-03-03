<?php

date_default_timezone_set('Europe/Paris');

// Création d'une constante plus courte qui est égale à DIRECTORY_SEPARATOR
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

// Définition du chemin vers le dossier application/
defined('APPLICATION_PATH') || define('APPLICATION_PATH', getenv('PREVARISC_APPLICATION_PATH') ? getenv('PREVARISC_APPLICATION_PATH') : dirname(__FILE__).DS.'..'.DS.'application');

// Define path to application directory
defined('DATA_PATH') || define('DATA_PATH', getenv('PREVARISC_DATA_PATH') ?  getenv('PREVARISC_DATA_PATH') : DS.'data');

// Define path to application directory
defined('REAL_DATA_PATH') || define('REAL_DATA_PATH', getenv('PREVARISC_REAL_DATA_PATH') ? getenv('PREVARISC_REAL_DATA_PATH') : dirname(__FILE__).DS.'..'.DS.'public'.DS.'data');

// Chargements des librairies
require APPLICATION_PATH.DS."..".DS."vendor".DS."autoload.php";

// Création de l'application avec les fichiers config
$application = new Zend_Application('production', array(
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
        'lifetime' => getenv('PREVARISC_CACHE_LIFETIME'),
    ),
    'security' => array(
        'salt' => getenv('PREVARISC_SECURITY_SALT'),
    ),
    'phpSettings' => array(
        'display_startup_errors' => getenv('PREVARISC_DEBUG_ENABLED'),
        'display_errors' => getenv('PREVARISC_DEBUG_ENABLED'),
    )
));
    
// Bootstrap et gooooo !
$application->bootstrap()->run();
