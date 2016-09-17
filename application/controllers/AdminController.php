<?php

class AdminController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $cache_config = $this->getInvokeArg('bootstrap')->getOption('cache');

        $this->_helper->layout->setLayout('menu_admin');
        
        if (getenv('PREVARISC_BRANCH') == false) {
            try {
                $git = new SebastianBergmann\Git\Git(APPLICATION_PATH . DS . '..');
                $revisions = $git->getRevisions();
                $last_revision = end($revisions);
                $revision_prevarisc_local = $last_revision['sha1'];
                $client = new Zend_Http_Client();
                $client->setUri('https://api.github.com/repos/SDIS62/prevarisc/git/refs/heads/2.x');
                $client->setConfig(['maxredirects' => 0, 'timeout' => 3]);
                $response = json_decode($client->request()->getBody());
                $revision_prevarisc_github = $response->object->sha;
                $this->view->is_uptodate = $revision_prevarisc_github == $revision_prevarisc_local;
            }
            catch(Exception $e) {}
        }

        $this->view->key_ign = getenv('PREVARISC_PLUGIN_IGNKEY');
        $this->view->key_googlemap = getenv('PREVARISC_PLUGIN_GOOGLEMAPKEY');
        $this->view->geoconcept_url = getenv('PREVARISC_PLUGIN_GEOCONCEPT_URL');
        $this->view->geoconcept_infos = array(
            'Url' => $this->view->geoconcept_url,
            'Layer' => getenv('PREVARISC_PLUGIN_GEOCONCEPT_LAYER'),
            'App ID' => getenv('PREVARISC_PLUGIN_GEOCONCEPT_APP_ID'),
            'Projection' => getenv('PREVARISC_PLUGIN_GEOCONCEPT_PROJECTION') ? : "Non paramétrée",
            'Token' => getenv('PREVARISC_PLUGIN_GEOCONCEPT_TOKEN'),
            'Geocoder Url' => getenv('PREVARISC_PLUGIN_GEOCONCEPT_GEOCODER'),
        );
        $this->view->dbname = getenv('PREVARISC_DB_DBNAME');
        $this->view->db_url = getenv('PREVARISC_DB_HOST').(getenv('PREVARISC_DB_PORT') ? ':'.getenv('PREVARISC_DB_PORT') : '');
        $this->view->api_enabled = getenv('PREVARISC_SECURITY_KEY') != "";
        $this->view->proxy_enabled = getenv('PREVARISC_PROXY_ENABLED');
        $this->view->third_party_plugins = implode(', ', explode(';', getenv('PREVARISC_THIRDPARTY_PLUGINS')));

        if (getenv('PREVARISC_CAS_ENABLED')) {
            $this->view->authentification = "CAS";
        } else if (getenv('PREVARISC_NTLM_ENABLED')) {
            $this->view->authentification = "NTLM + BDD";
        } else if (getenv('PREVARISC_LDAP_ENABLED')) {
            $this->view->authentification = sprintf("LDAP + BDD : %s:%d/%s", 
                getenv("PREVARISC_LDAP_HOST"), 
                getenv("PREVARISC_LDAP_PORT") ? : '389',
                getenv("PREVARISC_LDAP_BASEDN"));
        } else {
            $this->view->authentification = "BDD";
        }
        
        $this->view->cache_adapter = $cache_config['adapter'];
        $this->view->cache_url = $cache_config['host']. ($cache_config['port'] ? ':'.$cache_config['port'] : '');
        $this->view->cache_lifetime = $cache_config['lifetime'];
        $this->view->cache_enabled = $cache_config['enabled'];
        
        $this->view->enforce_security = getenv('PREVARISC_ENFORCE_SECURITY') == 1;

        $service_search = new Service_Search;
        $users = $service_search->users(null, null, null, true, 1000)['results'];
        $this->view->users = array();

        foreach ($users as $user) {
          if (time() - strtotime($user["LASTACTION_UTILISATEUR"]) < ini_get('session.gc_maxlifetime')) {
            $this->view->users[] = $user;
          }
        }
    }
}
