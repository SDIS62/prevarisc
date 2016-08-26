<?php

class AdminController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_helper->layout->setLayout('menu_admin');

        $options = Zend_Registry::get('options');

        try {
            $git = new SebastianBergmann\Git(APPLICATION_PATH . DS . '..');
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

        $this->view->key_ign = $options['carto']['ign'];
        $this->view->key_googlemap = $options['carto']['google'];
        $this->view->dbname = $options['resources']['db']['params']['dbname'];
        $this->view->db_url = $options['resources']['db']['params']['host'].($options['resources']['db']['params']['port'] ? ':'.$options['resources']['db']['params']['port'] : '');
        $this->view->api_enabled = $options['security']['key'];
        $this->view->proxy_enabled = $options['proxy']['enabled'];
        $this->view->third_party_plugins = implode(', ', $options['plugins']);

        if ($options['auth']['cas']['enabled']) {
            $this->view->authentification = "CAS";
        } else if ($options['auth']['ntlm']['enabled']) {
            $this->view->authentification = "NTLM + BDD";
        } else if ($options['auth']['ldap']['enabled']) {
            $this->view->authentification = sprintf("LDAP + BDD : %s:%d/%s",
                $options['auth']['ldap']['host'],
                $options['auth']['ldap']['port'],
                $options['auth']['ldap']['baseDn']);
        } else {
            $this->view->authentification = "BDD";
        }

        $this->view->cache_adapter = $options['cache']['adapter'];
        $this->view->cache_url = $options['cache']['host']. ($options['cache']['port'] ? ':'.$options['cache']['port'] : '');
        $this->view->cache_lifetime = $options['cache']['lifetime'];
        $this->view->cache_enabled = $options['cache']['enabled'];

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
