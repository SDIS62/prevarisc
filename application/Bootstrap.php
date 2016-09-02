<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @inheritdoc
     */
    public function run()
    {
        // Définit le décalage horaire par défaut de toutes les fonctions date/heure
        // sur celui de Paris
        date_default_timezone_set('Europe/Paris');

        // Chargement des plugins de base
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_View);
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_ACL);
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_XmlHttpRequest);

        // Chargement des plugins tiers
        foreach($this->getOption('plugins') as $thirdparty_plugin) {
            Zend_Controller_Front::getInstance()->registerPlugin(new $thirdparty_plugin);
        }

        // Stockage des options dans un registre global
        Zend_Registry::set('options', $this->getOptions());

        return parent::run();
    }

    /**
     * Initialisation de l'auto-loader
     */
    protected function _initAutoLoader()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();

        $autoloader_application = new Zend_Application_Module_Autoloader(array('basePath' => APPLICATION_PATH, 'namespace'  => null));

        $autoloader_application->addResourceType('cache', 'cache/', 'Cache');

        $autoloader->pushAutoloader($autoloader_application);

        return $autoloader;
    }

    /**
    * Initialisation du cache
    */
    protected function _initCache()
    {
        $options = $this->getOptions();

        $cache_options = $options['cache'];
        $db_options = $options['resources']['db'];

        if($cache_options['enabled'] && $cache_options['adapter'] == 'File') {
            $file = $cache_options['cache_dir'];
            if(!file_exists($file)) {
                mkdir($file);
            }
        }

        return Zend_Cache::factory(

            // front adapter
            'Core',

            // back adapter
            $cache_options['adapter'],

            // frontend options
            array(
                'caching'  => $cache_options['enabled'],
                'lifetime' => $cache_options['lifetime'],
                'cache_id_prefix' => 'prevarisc_'.md5($db_options['params']['dbname']).'_',
                'write_control' => $cache_options['write_control'],
            ),

            // backend options
            array(
                'servers' => array(
                    array(
                        'host' => $cache_options['host'],
                        'port' => $cache_options['port'],
                    ),
                ),
                'compression' => $cache_options['compression'],
                'read_control' => $cache_options['read_control'],
                'cache_dir' => $cache_options['cache_dir'],
                'cache_file_perm' => 0666,
                'hashed_directory_perm' => 0777,
            ),

            // use a custom name for front
            false,

            // use a custom name for back
            $cache_options['customAdapter'],

            // use application's autoload if an adapter is not loaded
            true

        );
    }

    /**
     * Initialisation de la vue
     */
    protected function _initView()
    {
        $view = new Zend_View();

        $view->headMeta()
            ->appendName('viewport', 'width=device-width,initial-scale=1')
            ->appendHttpEquiv('X-UA-Compatible', 'IE=edge,chrome=1')
            ->appendName('description', 'Logiciel de gestion du service Prévention')
            ->appendName('author', 'SDIS62 - Service Recherche et Développement');

        $view->addHelperPath(APPLICATION_PATH . "/views/helpers");

        return $view;
    }

    /**
     * Initialisation du layout
     */
    protected function _initLayout()
    {
        return Zend_Layout::startMvc(array('layoutPath' => APPLICATION_PATH . DS . 'views' . DS . 'layouts'));
    }

    /**
     * Initialisation du data store à utiliser
     */
    public function _initDataStore()
    {
        $options = $this->getOption('resources');
        $options = $options['dataStore'];
        $className = $options['adapter'];

        return new $className($options);
    }

    /**
    * Initialisation des ACL
    */
    protected function _initAcl()
    {
        require 'Acl.php';

        $acl = new Acl();

        return $acl;
    }
}
