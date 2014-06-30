<?php

class SessionController extends Zend_Controller_Action
{
    public function loginAction()
    {
        $this->_helper->layout->setLayout('login');

        $service_user = new Service_User;

        // Formulaire de connexion
        $form = new Form_Login;
        $this->view->form = $form;

        if($this->_request->isPost()) {

            try {

                // Erreur sur les données invalides
                if (!$form->isValid($this->_request->getPost())) {
                    throw new Zend_Auth_Exception('Données invalides.');
                }

                // Identifiants
                $username = $this->_request->prevarisc_login_username;
                $password = $this->_request->prevarisc_login_passwd;

                // Récupération de l'utilisateur
                $user = $service_user->findByUsername($username);

                // Si l'utilisateur n'est pas actif, on renvoie false
                if ($user === null || ($user !== null && !$user['ACTIF_UTILISATEUR'])) {
                    throw new Zend_Auth_Exception('L\'utilisateur n\'existe pas ou n\'est pas actif.');
                }

                try {
                    // Création de l'adapter d'authentification via LDAP
                    $adapter = new Zend_Auth_Adapter_Ldap();

                    if (getenv('PREVARISC_LDAP_ENABLED') != 1) {
                        throw new Zend_Auth_Exception('Authentification LDAP non activée');
                    }

                    // On associe notre ldap à l'adapter
                    $ldap = new Zend_Ldap(array(
                      'host' => getenv('PREVARISC_LDAP_HOST'),
                      'username' => getenv('PREVARISC_LDAP_USERNAME'),
                      'password' => getenv('PREVARISC_LDAP_PASSWORD'),
                      'baseDn' => getenv('PREVARISC_LDAP_BASEDN')
                    ));
                    $adapter->setLdap($ldap);

                    // On envoie les identifiants de connexion à l'adapter
                    $adapter->setUsername($ldap->getCanonicalAccountName($username, Zend_Ldap::ACCTNAME_FORM_DN));
                    $adapter->setPassword($password);

                    // Si l'identification s'est bien passée, on envoie l'objet Model_user correspondant à l'utilisateur courant
                    if (!$adapter->authenticate()->isValid()) {
                        throw new Zend_Auth_Exception('Les identifiants LDAP ne correspondent pas.');
                    }
                } catch (Exception $ee) {
                    // Si l'identification s'est bien passée, on envoie l'objet Model_user correspondant à l'utilisateur courant
                    if (md5($username . getenv('PREVARISC_SECURITY_SALT') . $password) != $user['PASSWD_UTILISATEUR']) {
                        throw new Zend_Auth_Exception('Les identifiants ne correspondent pas.');
                    }
                }

                // Stockage de l'utilisateur dans la session
                $storage = Zend_Auth::getInstance()->getStorage()->write($user);
                $this->_redirect(array("controller" => "index","action" => "index"));

            } catch (Exception $e) {
                $this->_helper->flashMessenger(array('context' => 'error','title' => 'Aie','message' => $e->getMessage()));
            }
        }

        $this->view->form = $form;
    }

    public function logoutAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $service_user = new Service_User;
        $service_user->updateLastActionDate(Zend_Auth::getInstance()->getIdentity()['ID_UTILISATEUR'], null);

        Zend_Auth::getInstance()->clearIdentity();

        $this->_helper->redirector->gotoUrl($this->view->url(array("controller" => null, "action" => null)));
    }
}
