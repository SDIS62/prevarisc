<?php

// Définition du chemin vers le dossier application/
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Définition de l'environnement de l'applciation
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
    
// Define path to application directory
defined('DATA_PATH')
    || define('DATA_PATH', '/data');
    
// Define path to application directory
defined('REAL_DATA_PATH')
    || define('REAL_DATA_PATH', realpath(dirname(__FILE__) . '/../public/data'));

// Création d'une constante plus courte qui est égale à DIRECTORY_SEPARATOR
defined('DS')
    || define('DS', DIRECTORY_SEPARATOR);
    
// Chargements des librairies
require APPLICATION_PATH . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php";

// Création de l'application avec les fichiers config
$application = new Zend_Application(APPLICATION_ENV, array('config' => array(
    APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'debug.ini',
    APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'application.ini',
    APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'db.ini',
    APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'secret.ini'
)));

// Bootstrap et gooooo !
$application->bootstrap()->run();