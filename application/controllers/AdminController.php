<?php

class AdminController extends Zend_Controller_Action
{
    public function indexAction()
    {
        // Récupération de la configuration
        $options = Zend_Registry::get('options');

        // On récupère la version actuelle.
        try {
            $git = new SebastianBergmann\Git($options['git_folder']);
            $branch_prevarisc = explode('/', $git->getCurrentBranch());
            $branch_prevarisc = end($branch_prevarisc);
            $revisions = $git->getRevisions();
            $last_revision = end($revisions);
            $this->view->revision_prevarisc_local = $last_revision['sha1'];
            $this->view->branch_prevarisc = $branch_prevarisc;
        }
        catch(Exception $e) {}

        // On ajoute à la vue les données utilisées par Prevarisc
        $this->view->key_ign = $options['carto']['ign'];
        $this->view->key_googlemap = $options['carto']['google'];
        $this->view->dbname = $options['resources']['db']['params']['dbname'];
        $this->view->db_url = $options['resources']['db']['params']['host'].($options['resources']['db']['params']['port'] ? ':'.$options['resources']['db']['params']['port'] : '');
        $this->view->api_enabled = $options['security']['key'];
        $this->view->proxy_enabled = $options['proxy']['enabled'];
        $this->view->third_party_plugins = implode(', ', $options['plugins']);
        $this->view->cache_adapter = $options['cache']['adapter'];
        $this->view->cache_url = $options['cache']['host']. ($options['cache']['port'] ? ':'.$options['cache']['port'] : '');
        $this->view->cache_lifetime = $options['cache']['lifetime'];
        $this->view->cache_enabled = $options['cache']['enabled'];

        // On envoie à la vue la méthode d'authentification utilisée par Prevarisc
        if ($options['auth']['cas']['enabled']) {
            $this->view->authentification = "CAS + BDD";
        } else if ($options['auth']['ntlm']['enabled']) {
            $this->view->authentification = "NTLM + BDD";
        } else if ($options['auth']['ldap']['enabled']) {
            $this->view->authentification = sprintf("LDAP (%s:%d/%s) + BDD",
                $options['auth']['ldap']['host'],
                $options['auth']['ldap']['port'],
                $options['auth']['ldap']['baseDn']);
        } else {
            $this->view->authentification = "BDD";
        }
    }
}
