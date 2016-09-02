<?php

class SessionController extends Zend_Controller_Action
{
    public function loginAction()
    {
        // Récupération de la configuration
        $options = Zend_Registry::get('options');

        // Layout login
        $this->_helper->layout->setLayout('login');

        // récupération du formulaire de login
        $form = new Form_Login;

        // Initialisation des services
        $service_user = new Service_User;

        // On envoie sur la vue les données nécessaires
        $this->view->form = $form;

        // Username et password à null par défaut
        $username = null;
        $password = null;

        // Booléen permettant de savoir si on est connecté via le CAS / NTLM
        $sso_authenticated = false;

        // On tente la connexion
        try {

            // Récupération de l'username avec CAS
            if ($options['auth']['cas']['enabled'] == 1) {
                try {
                    if ($options['debug'] == 1) {
                        phpCAS::setDebug();
                        phpCAS::setVerbose(true);
                    }
                    phpCAS::client(
                        $options['auth']['cas']['version'] ? : CAS_VERSION_2_0,
                        $options['auth']['cas']['host'],
                        (int) $options['auth']['cas']['port'],
                        $options['auth']['cas']['context'],
                        false
                    );
                    phpCAS::setLang(PHPCAS_LANG_FRENCH);
                    if ($options['auth']['cas']['no_server_validation'] == 1) {
                        phpCAS::setNoCasServerValidation();
                    }
                    phpCAS::forceAuthentication();
                    $username = phpCAS::getUser();
                    $sso_authenticated = true;
                }
                catch(Exception $e) {}
            }

            // Récupération de l'username avec NTLM
            if ($options['auth']['ntlm']['enabled'] == 1) {
                try {
                    if (isset($_SERVER['REMOTE_USER'])) {
                        $cred = explode('\\', $_SERVER['REMOTE_USER']);
                        if (count($cred) == 1) array_unshift($cred, null);
                        list($domain, $username) = $cred;
                        $sso_authenticated = true;
                    }
                }
                catch(Exception $e) {}
            }

            // Récupération de l'username avec le formulaire
            if ($this->_request->isPost()) {
                // Si le formulaire envoyé est non valide, on lève une exception
                if (!$form->isValid($this->_request->getPost())) {
                    throw new Zend_Auth_Exception('Données invalides.');
                }
                // Identifiants récupérés
                $username = $this->_request->prevarisc_login_username;
                $password = $this->_request->prevarisc_login_passwd;
            }

            // Si on a un username
            if ($username) {

                // Récupération de l'utilisateur
                $user = $service_user->findByUsername($username);

                // Si l'utilisateur n'est pas actif, on renvoie false
                if ($user === null || ($user !== null && !$user['ACTIF_UTILISATEUR'])) {
                    throw new Exception('L\'utilisateur n\'existe pas ou n\'est pas actif.');
                }

                // Authentification adapters
                $adapters = array();

                // Adaptateur principal (dbtable)
                $adapters['dbtable'] = new Zend_Auth_Adapter_DbTable(null, 'utilisateur', 'USERNAME_UTILISATEUR', 'PASSWD_UTILISATEUR');
                $salt = $options['security']['salt'];
                $adapters['dbtable']->setIdentity($username)->setCredential(md5($username . $salt . $password));

                // Adaptateur LDAP
                if ($options['auth']['ldap']['enabled'] == 1) {
                    $ldap = new Zend_Ldap(array(
                        'host' => $options['auth']['ldap']['host'],
                        'port' => $options['auth']['ldap']['port'],
                        'username' => $options['auth']['ldap']['username'],
                        'password' => $options['auth']['ldap']['password'],
                        'baseDn' => $options['auth']['ldap']['baseDn']
                    ));
                    try {
                        $accountForm = $options['auth']['ldap']['account_form'] ? $options['auth']['ldap']['account_form'] : Zend_Ldap::ACCTNAME_FORM_DN;
                        $adapters['ldap'] = new Zend_Auth_Adapter_Ldap();
                        $adapters['ldap']->setLdap($ldap);
                        $adapters['ldap']->setUsername($ldap->getCanonicalAccountName($username, $accountForm));
                        $adapters['ldap']->setPassword($password);
                    } catch (Exception $e) {}
                }

                // On lance le process d'identification avec les différents adaptateurs
                foreach ($adapters as $key => $adapter) {
                    if ($adapter->authenticate()->isValid() || $sso_authenticated) {
                        $storage = Zend_Auth::getInstance()->getStorage()->write($user);
                        $this->_helper->redirector->gotoUrl(empty($this->_request->getParams()["redirect"]) ? '/' : urldecode($this->_request->getParams()["redirect"]));
                    }
                }

                throw new Exception('Les identifiants ne correspondent pas.');
            }

        } catch (Exception $e) {
            $this->_helper->flashMessenger(array('context' => 'danger', 'title' => 'Erreur d\'authentification', 'message' => $e->getMessage()));
        }
    }

    public function logoutAction()
    {
        // Récupération des options
        $options = Zend_Registry::get('options');

        // Récupération du composant Auth
        $auth = Zend_Auth::getInstance();

        // Si l'utilisateur est connecté, on le logout
        if($auth->hasIdentity()) {
            $service_user = new Service_User;
            $service_user->updateLastActionDate($auth->getIdentity()['ID_UTILISATEUR'], null);
            $auth->clearIdentity();
            if ($options['auth']['cas']['enabled'] == 1) {
                phpCAS::logout();
            }
        }

        // Redirection vers l'index de Prevarisc
        $this->_helper->redirector('index', 'index');
    }
}
