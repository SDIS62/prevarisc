<?php
    class UserController extends Zend_Controller_Action
    {
        public function init()
        {
            $ajaxContext = $this->_helper->getHelper('AjaxContext');
            $ajaxContext->addActionContext('process', 'json')
                        ->addActionContext('getpreventionniste', 'json')
                        ->addActionContext('edit-avatar', 'html')
                        ->initContext();
        }

        public function editAction()
        {

            $this->_helper->layout->setLayout("menu_left");

            // Récupération des paramètres
            $ldap_options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('ldap');
            $this->view->params = array("LDAP_ACTIF" => $ldap_options['enabled']);

            $DB_user = new Model_DbTable_Utilisateur;
            $DB_informations = new Model_DbTable_UtilisateurInformations;

            $user = $DB_user->find( $this->getRequest()->getParam('uid') )->current();

            $this->view->user = $user;
            $this->view->user_info = $DB_informations->find( $user->ID_UTILISATEURINFORMATIONS )->current();
            $this->view->ldap = isset($this->_request->ldap) ? true : false;

            // Récupération de la commune
            $model_commune = new Model_DbTable_AdresseCommune;
            $this->view->commune = $model_commune->find($user->NUMINSEE_COMMUNE)->current();

            // Récupération des commissions et des groupements
            $model_commissions = new Model_DbTable_Commission;
            $this->view->rowset_commissions = $model_commissions->fetchAll();
            $this->view->rowset_commissionsUser = $DB_user->getCommissions($user->ID_UTILISATEUR);
            $model_groupements = new Model_DbTable_Groupement;
            $this->view->rowset_groupements = $model_groupements->fetchAll();
            $this->view->rowset_groupementsUser = $DB_user->getGroupements($user->ID_UTILISATEUR);

            $this->view->title = 'Modifier un utilisateur';

            $this->render('add');

        }

        public function editMeAction()
        {
            $user = Zend_Auth::getInstance()->getIdentity();
            $this->_helper->redirector->gotoUrl("/user/edit/uid/" . $user->ID_UTILISATEUR . ($this->_request->ldap == 1 ? "/ldap/1" : "") . "?ref=" . $user->ID_UTILISATEUR);
        }

        public function editAvatarAction()
        {
            $this->_helper->layout->disableLayout();

            if ($_FILES) {

                if ($_FILES["AVATAR"]["size"] < 1024 * 1024) {

                    GD_resize::run($_FILES["AVATAR"]["tmp_name"], REAL_DATA_PATH . "/uploads/avatars/small/" . $_POST["id"] . ".jpg", 25, 25);
                    GD_resize::run($_FILES["AVATAR"]["tmp_name"], REAL_DATA_PATH . "/uploads/avatars/medium/" . $_POST["id"] . ".jpg", 150);
                    GD_resize::run($_FILES["AVATAR"]["tmp_name"], REAL_DATA_PATH . "/uploads/avatars/large/" . $_POST["id"] . ".jpg", 224);

                    // CALLBACK
                    echo "<script type='text/javascript'>window.top.window.callback();</script>";
                }
            }

        }

        private function getDate($input)
        {
            $array_date = explode("/", $input);

            return $array_date[2]."-".$array_date[1]."-".$array_date[0]." 00:00:00";
        }

        public function processAction()
        {
            try {
                $DB_user = new Model_DbTable_Utilisateur;
                $DB_informations = new Model_DbTable_UtilisateurInformations;

                $user = $info = $id = null;

                if ($this->_request->uid) {

                    $user = $DB_user->find( $this->getRequest()->getParam('uid') )->current();
                    $info = $DB_informations->find( $user->ID_UTILISATEURINFORMATIONS )->current();

                    $info->setFromArray(array_intersect_key($_POST, $DB_informations->info('metadata')));

                    $info->DATE_PRV2 = isset($this->_request->DATE_PRV2) ? $this->getDate($this->_request->DATE_PRV2) : "0000-00-00 00:00:00";
                    $info->DATE_RECYCLAGE = isset($this->_request->DATE_RECYCLAGE) ? $this->getDate($this->_request->DATE_RECYCLAGE) : "0000-00-00 00:00:00";
                    $info->DATE_SID = isset($this->_request->DATE_SID) ? $this->getDate($this->_request->DATE_SID) : "0000-00-00 00:00:00";

                    $info->save();

                    $array_data = array_intersect_key($_POST, $DB_user->info('metadata'));

                    if(!empty($_POST["PASSWD_INPUT"]))
                        $array_data["PASSWD_UTILISATEUR"] = md5($user->USERNAME_UTILISATEUR."7aec3ab8e8d025c19e8fc8b6e0d75227".$this->_request->PASSWD_INPUT);
                    elseif(isset($this->_request->ldap_checkbox))
                        $array_data["PASSWD_UTILISATEUR"] = null;

                    $user->setFromArray($array_data)->save();
                    $iduser = $this->getRequest()->getParam('uid');

                } else {

                    if ($this->_request->maire == 1) {

                        if (empty($this->_request->NUMINSEE_COMMUNE)) {

                            throw new Exception('Aucune commune donnée', 500);
                        }

                        $model = new Model_DbTable_AdresseCommune;
                        $commune = $model->find($this->_request->NUMINSEE_COMMUNE)->current();

                        if ($commune != null) {

                            if ($commune->ID_UTILISATEURINFORMATIONS != 0 && $commune->ID_UTILISATEURINFORMATIONS != null) {

                                $info = $DB_informations->find( $commune->ID_UTILISATEURINFORMATIONS )->current();
                                $info->setFromArray(array_intersect_key($_POST, $DB_informations->info('metadata')));
                                $id = $commune->ID_UTILISATEURINFORMATIONS;
                            }
                        }

                        $_POST["ID_GROUPE"] = 1;
                    }

                    if ($id== null) {
                        $id = $DB_informations->insert(array_intersect_key($_POST, $DB_informations->info('metadata')));
                        $info = $DB_informations->find( $id )->current();
                        $info->DATE_PRV2 = isset($this->_request->DATE_PRV2) ? $this->getDate($this->_request->DATE_PRV2) : "0000-00-00 00:00:00";
                        $info->DATE_RECYCLAGE = isset($this->_request->DATE_RECYCLAGE) ? $this->getDate($this->_request->DATE_RECYCLAGE) : "0000-00-00 00:00:00";
                        $info->DATE_SID = isset($this->_request->DATE_SID) ? $this->getDate($this->_request->DATE_SID) : "0000-00-00 00:00:00";
                        $info->save();

                        if ($this->_request->maire == 1) {
                            $commune->ID_UTILISATEURINFORMATIONS = $id;
                            $commune->save();
                        }
                    }

                    $iduser = $DB_user->insert(array_merge(
                        array_intersect_key($_POST, $DB_user->info('metadata')),
                        array(
                            "ID_UTILISATEURINFORMATIONS" => $id,
                            "PASSWD_UTILISATEUR" => !empty($this->_request->PASSWD_INPUT) ? md5($this->_request->USERNAME_UTILISATEUR."7aec3ab8e8d025c19e8fc8b6e0d75227".$this->_request->PASSWD_INPUT) : null
                        )
                    ));
                }

                // Sauvegarde des commissions
                if (isset($_POST["commissions"])) {
                    $model_commissionsUser = new Model_DbTable_UtilisateurCommission;
                    $model_commissionsUser->delete("ID_UTILISATEUR = " .  $iduser);
                    foreach ($_POST["commissions"] as $id) {
                        $row = $model_commissionsUser->createRow();
                        $row->ID_UTILISATEUR = $iduser;
                        $row->ID_COMMISSION = $id;
                        $row->save();
                    }
                }

                // Sauvegarde des groupements
                if (isset($_POST["groupements"])) {
                    $model_groupementsUser = new Model_DbTable_UtilisateurGroupement;
                    $model_groupementsUser->delete("ID_UTILISATEUR = " .  $iduser);
                    foreach ($_POST["groupements"] as $id) {
                        $row = $model_groupementsUser->createRow();
                        $row->ID_UTILISATEUR = $iduser;
                        $row->ID_GROUPEMENT = $id;
                        $row->save();
                    }
                }

                $this->_helper->flashMessenger(array(
                    'context' => 'success',
                    'title' => 'Sauvegarde réussie !',
                    'message' => 'Les informations de l\utilisateur '.$DB_user->USERNAME_UTILISATEUR.' ont été enregistrées.'
                ));

            } catch (Exception $e) {
                $this->_helper->flashMessenger(array(
                    'context' => 'error',
                    'title' => 'Erreur lors de la sauvegarde des informations',
                    'message' => $e->getMessage()
                ));
            }

            //redirection
            $this->_helper->redirector('user');
        }

        public function profileAction()
        {
                // Modèles
                $DB_user = new Model_DbTable_Utilisateur;
                $DB_informations = new Model_DbTable_UtilisateurInformations;
                $DB_groupe = new Model_DbTable_Groupe;

                // Récupération des données
                $user = $DB_user->find( $this->_request->uid )->current();

                if (Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR == $user->ID_UTILISATEUR && $this->_request->me != true) {
                    $this->_helper->_redirector("me");
                }

                // A t'on le droit de modifier ?
                $this->view->user = $user;
                $this->view->user_info = $DB_informations->find( $user->ID_UTILISATEURINFORMATIONS )->current();
                $this->view->groupe = $DB_groupe->find( $user["ID_GROUPE"] )->current();

                // Récupération des utilisateurs du groupe de l'user
                $this->view->users = $DB_user->getUsersWithInformations( $user->ID_GROUPE );

                // Etablissements liés
                $etablissements = array();

                // Ets 1 - 4ème catégorie
                $search = new Model_DbTable_Search;
                $search->setItem("etablissement");
                $search->setCriteria("utilisateur.ID_UTILISATEUR", $this->_request->uid);
                $search->setCriteria("etablissementinformations.ID_CATEGORIE", array("1","2","3","4"));
                $search->setCriteria("etablissementinformations.ID_GENRE", 2);
                $etablissements = array_merge($search->run(null, null, false)->toArray(), $etablissements);

                // 5ème catégorie defavorable
                $search = new Model_DbTable_Search;
                $search->setItem("etablissement");
                $search->setCriteria("utilisateur.ID_UTILISATEUR", $this->_request->uid);
                $search->setCriteria("etablissementinformations.ID_CATEGORIE", "5");
                $search->setCriteria("avis.ID_AVIS", 2);
                $search->setCriteria("etablissementinformations.ID_GENRE", 2);
                $etablissements = array_merge($search->run(null, null, false)->toArray(), $etablissements);

                // 5ème catégorie avec local à sommeil
                $search = new Model_DbTable_Search;
                $search->setItem("etablissement");
                $search->setCriteria("utilisateur.ID_UTILISATEUR", $this->_request->uid);
                $search->setCriteria("etablissementinformations.ID_CATEGORIE", "5");
                $search->setCriteria("etablissementinformations.ID_GENRE", 2);
                $search->setCriteria("etablissementinformations.LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS", "1");
                $etablissements = array_merge($search->run(null, null, false)->toArray(), $etablissements);

                // EIC - IGH - HAB
                $search = new Model_DbTable_Search;
                $search->setItem("etablissement");
                $search->setCriteria("utilisateur.ID_UTILISATEUR", $this->_request->uid);
                $search->setCriteria("etablissementinformations.ID_GENRE", array("6","5","4"));
                $etablissements = array_merge($search->run(null, null, false)->toArray(), $etablissements);

                $this->view->etablissements = $etablissements;

                // Définition du titre de la page.
                $this->view->title = $this->view->user_info->NOM_UTILISATEURINFORMATIONS . " " . $this->view->user_info->PRENOM_UTILISATEURINFORMATIONS;
        }

        public function meAction()
        {
            $user = Zend_Auth::getInstance()->getIdentity();
            $this->_helper->redirector('profile', null, null, array( 'uid' => $user->ID_UTILISATEUR, 'me' => true ));

        }

        public function loginAction()
        {
            // Formulaire de connexion
            $form = new Form_Login;
            $this->view->form = $form;

            // Instance de Zend_Auth
            $auth = Zend_Auth::getInstance();

            // Définition du layout
            $this->_helper->layout->setLayout('login');

            if (!$this->_request->isPost()) {
                return;
            }

            try {
                if (!$form->isValid($this->_request->getPost())) {
                    throw new Zend_Auth_Exception('Données invalides.');
                }

                // Modèles de données
                $model_utilisateurInformations = new Model_DbTable_UtilisateurInformations;
                $model_utilisateur = new Model_DbTable_Utilisateur;
                $model_groupe = new Model_DbTable_Groupe;

                // Identifiants
                $username = $this->_request->username;
                $password = $this->_request->passwd;

                // Récupération de l'utilisateur
                $user = $model_utilisateur->fetchRow($model_utilisateur->select()->where('USERNAME_UTILISATEUR = ?', $username));

                // Si l'utilisateur n'est pas actif, on renvoie false
                if ($user === null || !$user->ACTIF_UTILISATEUR) {
                    throw new Zend_Auth_Exception('L\'utilisateur n\'existe pas ou n\'est pas actif.');
                }

                // Création de l'adapter d'authentification via LDAP
                $adapter = new Zend_Auth_Adapter_Ldap();

                try {
                    // Récupération des paramètres LDAP
                    $ldap_options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('ldap');

                    if ($ldap_options['enabled'] != 1) {
                        throw new Zend_Auth_Exception('Authentification LDAP non activée');
                    }

                    unset($ldap_options['enabled']);

                    // On associe notre ldap à l'adapter
                    $ldap = new Zend_Ldap($ldap_options);
                    $adapter->setLdap($ldap);

                    // On envoie les identifiants de connexion à l'adapter
                    $adapter->setUsername($ldap->getCanonicalAccountName($username, Zend_Ldap::ACCTNAME_FORM_DN));
                    $adapter->setPassword($password);

                    // Si l'identification s'est bien passée, on envoie l'objet Model_user correspondant à l'utilisateur courant
                    if (!$adapter->authenticate()->isValid()) {
                        throw new Zend_Auth_Exception('Les identifiants LDAP ne correspondent pas.');
                    }
                } catch (Exception $ee) {
                    // Si l'utilisateur est stocké en base, on analyse la correspondance du mot de passe entre celui en base et celui fourni
                    $config_security = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('security');

                    // Si l'identification s'est bien passée, on envoie l'objet Model_user correspondant à l'utilisateur courant
                    if (md5($username . $config_security['salt'] . $password) != $user->PASSWD_UTILISATEUR) {
                        throw new Zend_Auth_Exception('Les identifiants ne correspondent pas.');
                    }
                }

                // Message de bienvenue
                $this->_helper->flashMessenger(array(
                    'context' => 'success',
                    'title' => 'Bonjour !',
                    'message' => 'Vous êtes bien connecté sur votre compte. Bienvenue sur Prevarisc !'
                ));

                // Stockage de l'utilisateur dans la session
                $row_utilisateurInformations = $model_utilisateurInformations->find( $user->ID_UTILISATEURINFORMATIONS )->current();
                $row_groupe = $model_groupe->find( $user->ID_GROUPE )->current();
                $storage = $auth->getStorage()->write((object) array(
                    "ID_UTILISATEUR" => $user->ID_UTILISATEUR,
                    "NOM_UTILISATEURINFORMATIONS" => $row_utilisateurInformations->NOM_UTILISATEURINFORMATIONS,
                    "PRENOM_UTILISATEURINFORMATIONS" => $row_utilisateurInformations->PRENOM_UTILISATEURINFORMATIONS,
                    "LIBELLE_GROUPE" => $row_groupe->LIBELLE_GROUPE,
                    "ID_GROUPE" => $row_groupe->ID_GROUPE
                ));

                // Redirection
                $this->_redirect(array("controller" => "index","action" => "index"));
            } catch (Exception $e) {
                $this->_helper->flashMessenger(array(
                    'context' => 'error',
                    'title' => 'Aie',
                    'message' => $e->getMessage()
                ));
            }

            $this->view->form = $form;
        }

        // Logout
        public function logoutAction()
        {
            // On update la dernière action effectuée par l'utilisateur
            $model_user = new Model_DbTable_Utilisateur;
            $user = $model_user->find(Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR)->current();
            $user->LASTACTION_UTILISATEUR = null;
            $user->save();

            $this->_helper->viewRenderer->setNoRender();
            Zend_Auth::getInstance()->clearIdentity();

            $this->_helper->redirector->gotoUrl($this->view->url(array("controller" => null, "action" => null)));
        }

        public function getpreventionnisteAction()
        {
            // Création de l'objet recherche
            $search = new Model_DbTable_Search;

            // On set le type de recherche
            $search->setItem("utilisateur");
            $search->limit(5);

            // On cherche les préventionnistes
            $search->setCriteria("fonction.ID_FONCTION", 13);

            // On recherche avec le libellé
            $search->setCriteria("NOM_UTILISATEURINFORMATIONS", $this->_request->q, false);

            // On balance le résultat sur la vue
            $this->view->resultats = $search->run()->getAdapter()->getItems(0, 99999999999)->toArray();

        }
    }
