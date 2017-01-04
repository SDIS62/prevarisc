<?php

class SessionController extends Zend_Controller_Action
{
    public function loginAction()
    {
        $this->_helper->layout->setLayout('login');

        $form = new Form_Login;
        $service_user = new Service_User;
        $username = null;
        $this->view->form = $form;
        
        try {
            
            $username = null;
            $password = "";
            
            // Adaptateur CAS
            if (getenv('PREVARISC_CAS_ENABLED') == 1) {
                $username = phpCAS::getUser();
                
            } else if (getenv('PREVARISC_NTLM_ENABLED') == 1) {
                
                if (!isset($_SERVER['REMOTE_USER'])) {
                    error_log('ntlm auth with no REMOTE_USER set in server variables');
                } else {
                    $cred = explode('\\', $_SERVER['REMOTE_USER']); 
                    if (count($cred) == 1) array_unshift($cred, null); 
                    list($domain, $username) = $cred;
                }
                
            }
            
            if ($this->_request->isPost()) {
                
                if (!$form->isValid($this->_request->getPost())) {
                    throw new Zend_Auth_Exception('Données invalides.');
                }
                // Identifiants
                $username = $this->_request->prevarisc_login_username;
                $password = $this->_request->prevarisc_login_passwd;
            }
            
            if ($username) {
            
                // Récupération de l'utilisateur
                $user = $service_user->findByUsername($username);

                // Si l'utilisateur n'est pas actif, on renvoie false
                if ($user === null || ($user !== null && !$user['ACTIF_UTILISATEUR'])) {
                    throw new Exception('L\'utilisateur n\'existe pas ou n\'est pas actif.');
                }

                // Authentification adapters
                $adapters = array();

                // Adaptateur SSO noauth
                if (getenv('PREVARISC_CAS_ENABLED') == 1 || getenv('PREVARISC_NTLM_ENABLED') == 1 ) {
                    $adapters['sso'] = new Service_PassAuthAdapater($username);
                }

                // Adaptateur principal (dbtable)
                $adapters['dbtable'] = new Zend_Auth_Adapter_DbTable(null, 'utilisateur', 'USERNAME_UTILISATEUR', 'PASSWD_UTILISATEUR');
                $adapters['dbtable']->setIdentity($username)->setCredential(md5($username . getenv('PREVARISC_SECURITY_SALT') . $password));

                // Adaptateur LDAP
                if (getenv('PREVARISC_LDAP_ENABLED') == 1) {
                    $ldap = new Zend_Ldap(array('host' => getenv('PREVARISC_LDAP_HOST'), 'port' => getenv('PREVARISC_LDAP_PORT') ? : 389, 'username' => getenv('PREVARISC_LDAP_USERNAME'), 'password' => getenv('PREVARISC_LDAP_PASSWORD'), 'baseDn' => getenv('PREVARISC_LDAP_BASEDN'), 'bindRequiresDn' => getenv('PREVARISC_LDAP_BIND_REQUIRES_DN')));
                    try {
                        $accountForm = getenv('PREVARISC_LDAP_ACCOUNT_FORM') ? getenv('PREVARISC_LDAP_ACCOUNT_FORM') : Zend_Ldap::ACCTNAME_FORM_DN;
                        $adapters['ldap'] = new Zend_Auth_Adapter_Ldap();
                        $adapters['ldap']->setLdap($ldap);
                        $adapters['ldap']->setUsername($ldap->getCanonicalAccountName($username, $accountForm));
                        $adapters['ldap']->setPassword($password);
                    } catch (Exception $e) {}
                }

                // On lance le process d'identification avec les différents adaptateurs
                foreach ($adapters as $key => $adapter) {
                    if ($adapter->authenticate()->isValid()) {
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
        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()) {
            $service_user = new Service_User;

            $service_user->updateLastActionDate($auth->getIdentity()['ID_UTILISATEUR'], null);

            $auth->clearIdentity();
        }
        
        if (getenv('PREVARISC_CAS_ENABLED') == 1) {
            phpCAS::logout();
        } else {
            $this->_helper->redirector->gotoUrl($this->view->url(array("controller" => null, "action" => null)));
        }
    }
}

