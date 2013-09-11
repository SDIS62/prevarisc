<?php
    class UserController extends Zend_Controller_Action
    {
        public function init()
        {
            $ajaxContext = $this->_helper->getHelper('AjaxContext');
            $ajaxContext->addActionContext('add', 'json')
                        ->addActionContext('getpreventionniste', 'json')
                        ->addActionContext('process', 'json')
                        ->addActionContext('edit-avatar', 'html')
                        ->addActionContext('is-active', 'json')
                        ->addActionContext('get-group', 'html')
                        ->addActionContext('save-group', 'json')
                        ->addActionContext('login', 'json')
                        ->initContext();

            // On check si l'utilisateur peut accéder à cette partie
            if(!in_array($this->_request->getActionName(), array("me", "edit-me", "profile", "edit-avatar", "is-active", "logout", "login", "getpreventionniste", "process", "edit")) && $this->_helper->Droits()->get()->DROITADMINSYS_GROUPE == 0)
                $this->_helper->Droits()->redirect();
        }

        public function indexAction()
        {
            $this->view->title = 'Gestion des utilisateurs';

            $DB_groupe = new Model_DbTable_Groupe;
            $this->view->groupes = $DB_groupe->fetchAll()->toArray();
        }

        public function addGroupAction()
        {
            if ($this->_request->gid) {
                $DB_groupe = new Model_DbTable_Groupe;
                $this->view->groupe = $DB_groupe->find( $this->_request->gid )->current();
            }
        }
        
        public function deleteGroupAction()
        {
            $DB_user = new Model_DbTable_Utilisateur;
            $DB_groupe = new Model_DbTable_Groupe;
            
            if ($this->_request->gid && $this->_request->gid != 1) {

                $all = $DB_user->fetchAll("ID_GROUPE = " . $this->_request->gid);

                // On bouge les users dans le groupe par défaut
                if ($all != null) {

                    foreach ( $all->toArray() as $item ) {

                        $user = $DB_user->find( $item["ID_UTILISATEUR"] )->current();
                        $user->ID_GROUPE = 1;
                        $user->save();
                    }
                }
                
                // On supprime le groupe
                $DB_groupe->delete( $this->_request->gid );
            }
            
            $this->_helper->redirector->gotoUrl("/user");
        }

        public function saveGroupAction()
        {
            $DB_groupe = new Model_DbTable_Groupe;
            if ( !empty($this->_request->gid) ) {
                $groupe = $DB_groupe->find( $this->_request->gid )->current();
                $groupe->setFromArray(array_intersect_key($_POST, $DB_groupe->info('metadata')))->save();
            } else {
                $DB_groupe->insert(array_intersect_key($_POST, $DB_groupe->info('metadata')));
            }
        }

        public function getGroupAction()
        {
            // Modèles
            $DB_user = new Model_DbTable_Utilisateur;
            $DB_groupe = new Model_DbTable_Groupe;

            // Utilisateurs du groupe
            $this->view->users = $DB_user->getUsersWithInformations( $this->_request->gid );

            // Information du groupe
            $this->view->groupe = $DB_groupe->find( $this->_request->gid )->current();
        }

        public function editAction()
        {
            $this->view->droitsSYS = $this->_helper->Droits()->get()->DROITADMINSYS_GROUPE;

            // Récupération des paramètres
            $model_admin = new Model_DbTable_Admin;
            $this->view->params = $model_admin->getParams();

            $DB_user = new Model_DbTable_Utilisateur;
            $DB_informations = new Model_DbTable_UtilisateurInformations;

            $user = $DB_user->find( $this->getRequest()->getParam('uid') )->current();

            if($this->_helper->Droits()->get()->DROITADMINSYS_GROUPE == 0 && Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR != $user->ID_UTILISATEUR)
                $this->_helper->Droits()->redirect();

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

                if($this->_helper->Droits()->get()->DROITADMINSYS_GROUPE == 0 && Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR != $_POST["id"])
                    $this->_helper->Droits()->redirect();

                require_once 'GD/GD_resize.php';

                if ($_FILES["AVATAR"]["size"] < 1024 * 1024) {

                    GD_resize($_FILES["AVATAR"]["tmp_name"], DATA_PATH . "/uploads/avatars/small/" . $_POST["id"] . ".jpg", 25, 25);
                    GD_resize($_FILES["AVATAR"]["tmp_name"], DATA_PATH . "/uploads/avatars/medium/" . $_POST["id"] . ".jpg", 150);
                    GD_resize($_FILES["AVATAR"]["tmp_name"], DATA_PATH . "/uploads/avatars/large/" . $_POST["id"] . ".jpg", 224);

                    // CALLBACK
                    echo "<script type='text/javascript'>window.top.window.callback();</script>";
                }
            }
        }

        public function addAction()
        {
            $this->view->title = 'Ajouter un utilisateur';

            // Récupération des paramètres
            $model_admin = new Model_DbTable_Admin;
            $this->view->params = $model_admin->getParams();

            // Récupération des commissions et des groupements
            $model_commissions = new Model_DbTable_Commission;
            $this->view->rowset_commissions = $model_commissions->fetchAll();
            $model_groupements = new Model_DbTable_Groupement;
            $this->view->rowset_groupements = $model_groupements->fetchAll();
        }

        public function maireAddAction()
        {
            $this->view->title = 'Ajouter un maire';
            $this->view->maire = true;

            // Récupération des commissions et des groupements
            $model_commissions = new Model_DbTable_Commission;
            $this->view->rowset_commissions = $model_commissions->fetchAll();
            $model_groupements = new Model_DbTable_Groupement;
            $this->view->rowset_groupements = $model_groupements->fetchAll();

            $this->render('add');
        }

        private function getDate($input)
        {
            $array_date = explode("/", $input);

            return $array_date[2]."-".$array_date[1]."-".$array_date[0]." 00:00:00";
        }

        public function processAction()
        {
            $DB_user = new Model_DbTable_Utilisateur;
            $DB_informations = new Model_DbTable_UtilisateurInformations;

            if(($this->_helper->Droits()->get()->DROITADMINSYS_GROUPE == 0 && !$this->_request->uid) || ($this->_helper->Droits()->get()->DROITADMINSYS_GROUPE == 0 && isset($this->_request->uid) && $this->_request->uid != Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR))
                $this->_helper->Droits()->redirect();

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

                //if( $this->_request->PASSWD )
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
        }

        public function activedGroupAction()
        {
            $DB_user = new Model_DbTable_Utilisateur;
            $all = $DB_user->fetchAll("ID_GROUPE = " . $this->_request->gid);

            if ($all != null) {

                foreach ( $all->toArray() as $item ) {

                    $user = $DB_user->find( $item["ID_UTILISATEUR"] )->current();
                    $user->ACTIF_UTILISATEUR = $this->_request->act;
                    $user->save();
                }
            }

            $this->_redirect("/user");
        }

        public function activedAction()
        {
            $DB_user = new Model_DbTable_Utilisateur;
            $user = $DB_user->find( $this->_request->uid )->current();
            $user->ACTIF_UTILISATEUR = !(bool) $user->ACTIF_UTILISATEUR;
            $user->save();
            $this->_redirect("/user");
        }

        public function isActiveAction()
        {
            $DB_user = new Model_DbTable_Utilisateur;
            $user = $DB_user->find( $this->_request->uid )->current();
            $this->view->active = $user ? $user->ACTIF_UTILISATEUR : false;
        }

        public function profileAction()
        {
            // Modèles
            $DB_user = new Model_DbTable_Utilisateur;
            $DB_informations = new Model_DbTable_UtilisateurInformations;
            $search = new Model_DbTable_Search;
            $DB_groupe = new Model_DbTable_Groupe;

            // Récupération des données
            $user = $DB_user->find( $this->_request->uid )->current();

            if (Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR == $user->ID_UTILISATEUR && $this->_request->me != true) {

                $this->_helper->_redirector("me");
            }

            // A t'on le droit de modifier ?
            $this->view->edit_bool = $this->_helper->Droits()->get()->DROITADMINSYS_GROUPE == 0 && Zend_Auth::getInstance()->getIdentity()->ID_UTILISATEUR != $user->ID_UTILISATEUR ? false : true;

            $this->view->user = $user;
            $this->view->user_info = $DB_informations->find( $user->ID_UTILISATEURINFORMATIONS )->current();
            $this->view->groupe = $DB_groupe->find( $user["ID_GROUPE"] )->current();

            // Récupération des utilisateurs du groupe de l'user
            $this->view->users = $DB_user->getUsersWithInformations( $user->ID_GROUPE );

            // Etablissements liés
            $search->setItem("etablissement");
            $search->setCriteria("utilisateur.ID_UTILISATEUR", $this->_request->uid);
            $this->view->etablissements = $search->run();

            // Définition du titre de la page.
            $this->view->title = $this->view->user_info->NOM_UTILISATEURINFORMATIONS . " " . $this->view->user_info->PRENOM_UTILISATEURINFORMATIONS;
        }

        public function meAction()
        {
            $user = Zend_Auth::getInstance()->getIdentity();
            $this->_forward('profile', null, null, array( 'uid' => $user->ID_UTILISATEUR, 'me' => true ));
        }

        public function loginAction()
        {
            try {

                // Modèles de données
                $model_utilisateurInformations = new Model_DbTable_UtilisateurInformations;
                $model_utilisateur = new Model_DbTable_Utilisateur;

                $bool_ldap = false;

                // Instance de Zend_Auth
                $auth = Zend_Auth::getInstance();

                // Définition du layout
                $this->_helper->layout->setLayout('layout.min');

                // On gère le message informant la deco automatique
                if ($this->_request->deactivated) {

                    $this->view->error = "Votre compte utilisateur a été désactivé par un administrateur ou a expiré. Dans ce cas, veuillez recharger la page afin de vous identifier à nouveau.<br/><button id='reload'>Recharger la page</button>";
                }

                // On vérifie donc si des données ont été postées
                if ($this->_request->isPost()) {

                    // Si il y a deux champs
                    if (!$this->_request->username || !$this->_request->passwd) {

                        $this->view->error = "Veuillez remplir tout les champs.";
                    } else {

                        // Récupère l'adpatateur d'authentification avec des paramètres de constructeur
                        $authAdapter = new Zend_Auth_Adapter_DbTable(
                            Zend_Db_Table::getDefaultAdapter(),	// Adaptateur de la base de données
                            'utilisateur',						// Table des utilisateurs
                            'USERNAME_UTILISATEUR',				// Login de l'utilisateur
                            'PASSWD_UTILISATEUR'				// Mot de passe de l'utilisateur
                        );

                        // On règle les valeurs d'entrées de l'identification
                        $authAdapter->setIdentity($this->_request->username)
                                    ->setCredential(md5($this->_request->username."7aec3ab8e8d025c19e8fc8b6e0d75227".$this->_request->passwd));

                        // Réalise la requête d'authentification, et sauvegarde le résultat
                        $result = $authAdapter->authenticate();

                        // On check la validité de l'identification
                        if ( !$result->isValid() ) {

                            // On regarde si l'user à un mdp
                            $mdp = false;
                            $userTemp =  $model_utilisateur->find($model_utilisateur->getId($this->_request->username))->current();
                            if($userTemp != null)
                                $mdp =$userTemp->PASSWD_UTILISATEUR != null ? true : false;
                            else {
                                $this->view->error = "Utilisateur inconnu.";
                            }

                            // Récupération des paramètres
                            $model_admin = new Model_DbTable_Admin;
                            $params = $model_admin->getParams();

                            if ($params["LDAP_ACTIF"] && !$mdp) {

                                // Li'dentification a échouée, on va essayer de se connecter au LDAP
                                $ldap = new Zend_Ldap(array(
                                    'host'                   => $params["LDAP_HOST"],
                                    'username'               => $params["LDAP_USERNAME"],
                                    'password'               => $params["LDAP_PASSWORD"],
                                    'baseDn'                 => $params["LDAP_BASEDN"], //CN=Users,
                                ));

                                // Est ce que l'user est dans la DB ?
                                $bool_isRegistered = $model_utilisateur->isRegistered($this->_request->username);

                                try {

                                    if ($bool_isRegistered || $params["LDAP_LOGIN"]) {

                                        $acctname = $ldap->getCanonicalAccountName($this->_request->username, Zend_Ldap::ACCTNAME_FORM_DN);	// Récupération du compte
                                        $ldap->bind($acctname, $this->_request->passwd);	// On essaye de se connecter au compte avec les ID
                                        $array_dn = Zend_Ldap_Dn::explodeDn($acctname);	// On récupère les infos

                                        if ($params["LDAP_LOGIN"] && !$bool_isRegistered) {

                                            // On créé le compte
                                            $id_user = $model_utilisateur->insert( array(
                                                "ID_UTILISATEURINFORMATIONS" => $model_utilisateurInformations->insert(array("NOM_UTILISATEURINFORMATIONS" => $array_dn[0]["CN"])),
                                                //"PASSWD_UTILISATEUR" => md5($this->_request->username."7aec3ab8e8d025c19e8fc8b6e0d75227".$this->_request->passwd),
                                                "PASSWD_UTILISATEUR" => "",
                                                "USERNAME_UTILISATEUR" => $this->_request->username,
                                                "ID_GROUPE" => 1
                                            ));

                                            // On récupère le row
                                            $row_utilisateur = $model_utilisateur->find($id_user)->current();

                                            $bool_ldap = true;
                                            $this->view->ldap = $bool_ldap;
                                            $this->view->error = null;
                                        } else {

                                            $row_utilisateur = $model_utilisateur->find($model_utilisateur->getId($this->_request->username))->current();
                                        }
                                    } else {

                                        $this->view->error = "Indentifiants invalides.";
                                    }

                                } catch (Zend_Ldap_Exception $zle) {

                                    $this->view->error = "Indentifiants LDAP invalides.";
                                }
                            } else {

                                $this->view->error = "Indentifiants invalides.";
                            }
                        } else {

                            // On récupère la ligne d'utilisateur
                            $row_utilisateur = $authAdapter->getResultRowObject();
                        }

                        if ( isset($row_utilisateur) ) {

                            // On check si l'utilisateur est déclaré comme actif
                            if (!$row_utilisateur->ACTIF_UTILISATEUR) {

                                $this->view->error = "<strong>Erreur:</strong> Votre compte utilisateur a été désactivé par un administrateur.";
                            } else {
                                // On récupère les informations complémentaires
                                $row_utilisateurInformations = $model_utilisateurInformations->find( $row_utilisateur->ID_UTILISATEURINFORMATIONS )->current();

                                // Stocke l'identité
                                $storage = $auth->getStorage()->write((object) array(
                                    "ID_UTILISATEUR" => $row_utilisateur->ID_UTILISATEUR,
                                    "NOM_UTILISATEURINFORMATIONS" => $row_utilisateurInformations->NOM_UTILISATEURINFORMATIONS,
                                    "PRENOM_UTILISATEURINFORMATIONS" => $row_utilisateurInformations->PRENOM_UTILISATEURINFORMATIONS,
                                    "ID_GROUPE" => $row_utilisateur->ID_GROUPE
                                ));

                                // On écrit dans la base de données le cookie de session
                                $model_utilisateur->update(
                                    array("LASTACTION_UTILISATEUR" => session_id()),
                                    "ID_UTILISATEUR = " . $row_utilisateur->ID_UTILISATEUR
                                );
                            }
                        }
                    }
                }

                // Si on est connecté, on retourne à la page index
                if ($auth->hasIdentity() && !$this->_request->format) {

                    if( $bool_ldap )
                        $this->_redirect(array("controller" => "user","action" => "editMe", "ldap" => 1));
                    else
                        $this->_redirect(array("controller" => "index","action" => "index"));
                }
            } catch (Zend_Exception $e) {

                $this->view->error = "<strong>Erreur:</strong> Un problème est survenu lors de la connexion à la base de données. (" . $e->getMessage() . ")";
            }

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
            $search->setCriteria("NOM_UTILISATEURINFORMATIONS", $this->_request->getQuery("q"), false);

            // On balance le résultat sur la vue
            $this->view->resultats = $search->run();
        }
    }
