<?php

/*
|--------------------------------------------------------------------------
| Autoload
|--------------------------------------------------------------------------
*/

require_once __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Constantes utilisées par l'application
|--------------------------------------------------------------------------
*/

// Création d'une constante plus courte qui est égale à DIRECTORY_SEPARATOR
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

// Définition du chemin vers le dossier application/
defined('APPLICATION_PATH') || define('APPLICATION_PATH', getenv('PREVARISC_APPLICATION_PATH') ? getenv('PREVARISC_APPLICATION_PATH') : dirname(__FILE__).DS.'..'.DS.'application');

// Définition du chemin vers le dossier data (pour le client)
defined('DATA_PATH') || define('DATA_PATH', getenv('PREVARISC_DATA_PATH') ?  getenv('PREVARISC_DATA_PATH') : DS.'data');

// Définition du chemin vers le dossier data (pour le serveur)
defined('REAL_DATA_PATH') || define('REAL_DATA_PATH', getenv('PREVARISC_REAL_DATA_PATH') ? getenv('PREVARISC_REAL_DATA_PATH') : dirname(__FILE__).DS.'..'.DS.'public'.DS.'data');

/*
|--------------------------------------------------------------------------
| Récupération du fichier config
|--------------------------------------------------------------------------
*/

$config = require_once __DIR__.'/../config/config.php';

/*
|--------------------------------------------------------------------------
| Bootstrap et gooooo !
|--------------------------------------------------------------------------
*/

(new Zend_Application('production', $config))->bootstrap()->run();
