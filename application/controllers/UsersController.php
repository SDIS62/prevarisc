<?php

class UsersController extends Zend_Controller_Action
{
    /**
     * @inheritdoc
     */  
    public function init()
    {
        // Définition du layout menu_left
        $this->_helper->layout->setLayout('menu_left');
    }
    
    /**
     * Liste des utilisateurs en fonction de leur groupe
     *
     */
    public function listAction()
    {
        // Modèles 
        $DB_groupe = new Model_DbTable_Groupe;
        $DB_user = new Model_DbTable_Utilisateur;
        $DB_groupe = new Model_DbTable_Groupe;
        
        // Récupération de l'ensemble des informations et on envoie sur la vue
        $this->view->groupes = $DB_groupe->fetchAll()->toArray();
        
        // Si on affiche un groupe en particulier, on envoie ses informations
        if($this->hasParam('gid'))
        {
            $this->view->users = $DB_user->getUsersWithInformations($this->_request->getParam('gid'));
            $this->view->groupe = $DB_groupe->find($this->_request->getParam('gid'))->current();
        }
        else
        {
            $this->view->users = $DB_user->getUsersWithInformations();
        }
    }

    /**
     * Ajouter un groupe
     *
     */
    public function addGroupAction()
    {
        // Modèles
        $DB_groupe = new Model_DbTable_Groupe;
        
        // Si un groupe est spécifié, on l'édite
        if($this->hasParam('gid'))
        {
            $this->view->groupe = $DB_groupe->find($this->_request->getParam('gid'))->current();
        }
    }
    
    /**
     * Supprimer un groupe
     *
     */
    public function deleteGroupAction()
    {
        // Modèles
        $DB_user = new Model_DbTable_Utilisateur;
        $DB_groupe = new Model_DbTable_Groupe;
        
        // SI un groupe est spécifié, on le supprime
        if($this->hasParam('gid') && $this->_request->getParam('gid') != 1)
        {
            // récupération de tous les utilisateurs du groupe à supprimer
            $all_users = $DB_user->fetchAll("ID_GROUPE = " . $this->_request->gid);

            // On bouge les users du groupe à supprimer dans le groupe par défaut
            if ($all_users != null)
            {
                foreach($all_users as $item)
                {
                    $user = $DB_user->find( $item->ID_UTILISATEUR )->current();
                    $user->ID_GROUPE = 1;
                    $user->save();
                }
            }
            
            // On supprime le groupe
            $DB_groupe->delete($this->_request->getParam('gid'));
        }
        
        // Redirection
        $this->_helper->redirector('list');
    }

    /**
     * Sauvegarder un groupe
     *
     */
    public function saveGroupAction()
    {
        // Modèles
        $DB_groupe = new Model_DbTable_Groupe;
        
        // On analyse si on ajoute ou on édite
        if($this->hasParam('gid'))
        {
            $groupe = $DB_groupe->find($this->_request->getParam('gid'))->current();
            $groupe->setFromArray(array_intersect_key($_POST, $DB_groupe->info('metadata')))->save();
        }
        else
        {
            $DB_groupe->insert(array_intersect_key($_POST, $DB_groupe->info('metadata')));
        }
        
        // Redirection
        $this->_helper->redirector('list');
    }

    /**
     * Éditer un utilisateur
     *
     */
    public function editAction()
    {
        // Modèles
        $DB_user = new Model_DbTable_Utilisateur;
        $DB_informations = new Model_DbTable_UtilisateurInformations;
        $model_commune = new Model_DbTable_AdresseCommune;
        $model_commissions = new Model_DbTable_Commission;
        $model_groupements = new Model_DbTable_Groupement;

        // Récupération de l'utilisateur
        $user = $DB_user->find($this->_request->getParam('uid'))->current();

        // Envoie sur la vue des informations de l'utilisateur
        $this->view->user = $user;
        $this->view->user_info = $DB_informations->find( $user->ID_UTILISATEURINFORMATIONS )->current();
        $ldap_options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('ldap');
        $this->view->params = array("LDAP_ACTIF" => $ldap_options['enabled']);
        $this->view->commune = $model_commune->find($user->NUMINSEE_COMMUNE)->current();

        // Récupération des commissions et des groupements
        $this->view->rowset_commissions = $model_commissions->fetchAll();
        $this->view->rowset_commissionsUser = $DB_user->getCommissions($user->ID_UTILISATEUR);
        $this->view->rowset_groupements = $model_groupements->fetchAll();
        $this->view->rowset_groupementsUser = $DB_user->getGroupements($user->ID_UTILISATEUR);
        
        // Rendu de la vue add
        $this->render('add');
    }

    /**
     * Ajouter un utilisateur
     *
     */
    public function addAction()
    {
        // Modèles
        $model_commissions = new Model_DbTable_Commission;
        $model_groupements = new Model_DbTable_Groupement;
        
        // Récupération des commissions et des groupements
        $this->view->rowset_commissions = $model_commissions->fetchAll();
        $this->view->rowset_groupements = $model_groupements->fetchAll();
        $ldap_options = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('ldap');
        $this->view->params = array("LDAP_ACTIF" => $ldap_options['enabled']);
    }

    /**
     * Ajouter un maire
     *
     */
    public function maireAddAction()
    {
        // Modèles
        $model_commissions = new Model_DbTable_Commission;
        $model_groupements = new Model_DbTable_Groupement;
        
        // On dit à la vue que nous avons affaire à un maire !
        $this->view->maire = true;

        // Récupération des commissions et des groupements
        $this->view->rowset_commissions = $model_commissions->fetchAll();
        $this->view->rowset_groupements = $model_groupements->fetchAll();

        // Rendu de la vue add
        $this->render('add');
    }

    /**
     * Sauvegarder un utilisateur
     *
     */
    public function processAction()
    {
        // Modèles
        $DB_user = new Model_DbTable_Utilisateur;
        $DB_informations = new Model_DbTable_UtilisateurInformations;
        $model = new Model_DbTable_AdresseCommune;

        $user = $info = $id = null;

        // Ajout ou édition ?
        if($this->hasParam('uid'))
        {
            $user = $DB_user->find( $this->getRequest()->getParam('uid') )->current();
            $info = $DB_informations->find( $user->ID_UTILISATEURINFORMATIONS )->current();

            $info->setFromArray(array_intersect_key($_POST, $DB_informations->info('metadata')));

            $info->DATE_PRV2 = isset($this->_request->DATE_PRV2) ? $this->getDate($this->_request->DATE_PRV2) : "0000-00-00 00:00:00";
            $info->DATE_RECYCLAGE = isset($this->_request->DATE_RECYCLAGE) ? $this->getDate($this->_request->DATE_RECYCLAGE) : "0000-00-00 00:00:00";
            $info->DATE_SID = isset($this->_request->DATE_SID) ? $this->getDate($this->_request->DATE_SID) : "0000-00-00 00:00:00";

            $info->save();

            $array_data = array_intersect_key($_POST, $DB_user->info('metadata'));

            if(!empty($_POST["PASSWD_INPUT"]))
            {
                $config_security = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('security');
                $array_data["PASSWD_UTILISATEUR"] = md5($user->USERNAME_UTILISATEUR . $config_security['salt'] . $this->_request->PASSWD_INPUT);
            }
            elseif(isset($this->_request->ldap_checkbox))
            {
                $array_data["PASSWD_UTILISATEUR"] = null;
            }

            $user->setFromArray($array_data)->save();
            $iduser = $this->getRequest()->getParam('uid');
        }
        else
        {
            if ($this->_request->maire == 1)
            {
                if(empty($this->_request->NUMINSEE_COMMUNE))
                {
                    throw new Exception('Aucune commune donnée', 500);
                }

                $commune = $model->find($this->_request->NUMINSEE_COMMUNE)->current();

                if ($commune != null)
                {
                    if ($commune->ID_UTILISATEURINFORMATIONS != 0 && $commune->ID_UTILISATEURINFORMATIONS != null)
                    {
                        $info = $DB_informations->find( $commune->ID_UTILISATEURINFORMATIONS )->current();
                        $info->setFromArray(array_intersect_key($_POST, $DB_informations->info('metadata')));
                        $id = $commune->ID_UTILISATEURINFORMATIONS;
                    }
                }
                
                $_POST["ID_GROUPE"] = 1;
            }

            if($id == null)
            {
                $id = $DB_informations->insert(array_intersect_key($_POST, $DB_informations->info('metadata')));
                $info = $DB_informations->find( $id )->current();
                $info->DATE_PRV2 = isset($this->_request->DATE_PRV2) ? $this->getDate($this->_request->DATE_PRV2) : "0000-00-00 00:00:00";
                $info->DATE_RECYCLAGE = isset($this->_request->DATE_RECYCLAGE) ? $this->getDate($this->_request->DATE_RECYCLAGE) : "0000-00-00 00:00:00";
                $info->DATE_SID = isset($this->_request->DATE_SID) ? $this->getDate($this->_request->DATE_SID) : "0000-00-00 00:00:00";
                $info->save();

                if ($this->_request->maire == 1)
                {
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
        if (isset($_POST["commissions"]))
        {
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
        if (isset($_POST["groupements"]))
        {
            $model_groupementsUser = new Model_DbTable_UtilisateurGroupement;
            $model_groupementsUser->delete("ID_UTILISATEUR = " .  $iduser);
            foreach ($_POST["groupements"] as $id) {
                $row = $model_groupementsUser->createRow();
                $row->ID_UTILISATEUR = $iduser;
                $row->ID_GROUPEMENT = $id;
                $row->save();
            }
        }
        
        // Redirection
        $this->_helper->redirector('list');
    }

    /**
     * Activer ou desactiver un utilisateur
     *
     */
    public function activedAction()
    {
        // Modèle
        $DB_user = new Model_DbTable_Utilisateur;
        
        // On trouve l'utilisateur à modifier
        $user = $DB_user->find( $this->_request->uid )->current();
        
        // On change son état
        $user->ACTIF_UTILISATEUR = !(bool) $user->ACTIF_UTILISATEUR;
        $user->ACTIF_UTILISATEUR = (int) $user->ACTIF_UTILISATEUR;
        
        // On sauvegarde
        $user->save();
        
        // Redirection
        $this->_helper->redirector('list');
    }
    
    /**
     * Gestion des droits des utilisateurs
     *
     */
    public function matriceDesDroitsAction()
    {
        // Modèles
        $model_groupes = new Model_DbTable_Groupe;
        $model_resource = new Model_DbTable_Resource;
        $model_groupes_privilege = new Model_DbTable_GroupePrivilege;

        // On envoit les données sur la vue
        $this->view->rowset_groupes = $model_groupes->fetchAll();
        $this->view->rowset_resources = $model_resource->fetchAll();
        $this->view->rowset_groupes_privilege = $model_groupes_privilege->fetchAll()->toArray();
        
        // Si des données sont envoyées, on procède à leur traitement
        if($this->_request->isPost())
        {
            try
            {
                foreach($this->_request->getParam('groupe') as $id_groupe => $privileges)
                { 
                    foreach($privileges as $id_privilege => $value_privilege)
                    {
                        $groupe_privilege_exists = $model_groupes_privilege->find($id_groupe, $id_privilege)->current() !== null;
                        
                        if($value_privilege == 1 && !$groupe_privilege_exists)
                        {
                            $row_groupe_priv = $model_groupes_privilege->createRow();
                            $row_groupe_priv->ID_GROUPE = $id_groupe;
                            $row_groupe_priv->id_privilege = $id_privilege;
                            $row_groupe_priv->save();
                        }
                        
                        if($value_privilege == 0 && $groupe_privilege_exists)
                        {
                            $model_groupes_privilege->delete('ID_GROUPE = ' . $id_groupe . ' AND id_privilege = ' . $id_privilege);
                        }
                    }
                }
                
                $this->_helper->flashMessenger(array(
                    'context' => 'success',
                    'title' => 'Mise à jour réussie !',
                    'message' => 'La matrice des droits a bien été mise à jour.'
                ));
            }
            catch(Exception $e)
            {
                $this->_helper->flashMessenger(array(
                    'context' => 'error',
                    'title' => 'Aie',
                    'message' => $e->getMessage()
                ));
            }
            
            // Redirection
            $this->_helper->redirector('matrice-des-droits');
        }
    }
    
    private function getDate($input)
    {
        $array_date = explode("/", $input);

        return $array_date[2]."-".$array_date[1]."-".$array_date[0]." 00:00:00";
    }
}