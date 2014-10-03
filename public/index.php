<?php

date_default_timezone_set('Europe/Paris');

// Définition du chemin vers le dossier application/
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(getenv('APPLICATION_PATH') ? getenv('APPLICATION_PATH') : dirname(__FILE__) . '/../application'));

// Define path to application directory
defined('DATA_PATH')
    || define('DATA_PATH', getenv('DATA_PATH') ?  getenv('DATA_PATH') : '/data');

// Define path to application directory
defined('REAL_DATA_PATH')
    || define('REAL_DATA_PATH', realpath(getenv('REAL_DATA_PATH') ? getenv('REAL_DATA_PATH') : dirname(__FILE__) . '/../public/data'));

// Création d'une constante plus courte qui est égale à DIRECTORY_SEPARATOR
defined('DS')
    || define('DS', DIRECTORY_SEPARATOR);

// Chargements des librairies
require APPLICATION_PATH . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

// Création de l'application avec les fichiers config
$application = new Zend_Application('production', array(
  'bootstrap' => array(
    'path' => APPLICATION_PATH . '/Bootstrap.php'
  ),
  'resources' => array(
    'frontController' => array(
      'controllerDirectory' => APPLICATION_PATH . '/controllers',
      'moduleDirectory' => APPLICATION_PATH . '/modules',
      'params' => array(
        'displayExceptions' => getenv('PREVARISC_DEBUG_ENABLED')
      )
    ),
    'db' => array(
      'adapter' => getenv('PREVARISC_DB_ADAPTER'),
      'params' => array(
        'host' => getenv('PREVARISC_DB_HOST'),
        'charset' => getenv('PREVARISC_DB_CHARSET'),
        'username' => getenv('PREVARISC_DB_USERNAME'),
        'password' => getenv('PREVARISC_DB_PASSWORD'),
        'dbname' => getenv('PREVARISC_DB_DBNAME')
      )
    ),
    'modules' => ''
  ),
  'ldap' => array(
    'enabled' => getenv('PREVARISC_LDAP_ENABLED'),
    'host' => getenv('PREVARISC_LDAP_ENABLED') ? getenv('PREVARISC_LDAP_HOST') : '',
    'username' => getenv('PREVARISC_LDAP_ENABLED') ? getenv('PREVARISC_LDAP_USERNAME') : '',
    'password' => getenv('PREVARISC_LDAP_ENABLED') ? getenv('PREVARISC_LDAP_PASSWORD') : '',
    'baseDn' => getenv('PREVARISC_LDAP_ENABLED') ? getenv('PREVARISC_LDAP_BASEDN') : ''
  ),
  'cache' => array(
    'lifetime' => getenv('PREVARISC_CACHE_LIFETIME')
  ),
  'security' => array(
    'salt' => getenv('PREVARISC_SECURITY_SALT')
  ),
  'phpSettings' => array(
    'display_startup_errors' => getenv('PREVARISC_DEBUG_ENABLED'),
    'display_errors' => getenv('PREVARISC_DEBUG_ENABLED')
  )
));

// Bootstrap et gooooo !
$application->bootstrap()->run();
