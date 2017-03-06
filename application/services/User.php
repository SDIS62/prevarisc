<?php

class Service_User
{
    /**
     * Récupération d'un utilisateur
     *
     * @param int $id_user
     * @return array
     */
    public function find($id_user)
    {
        // Récupération de la ressource cache à partir du bootstrap
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

        if(($user = unserialize($cache->load('user_id_' . $id_user))) === false) {

            $model_user = new Model_DbTable_Utilisateur;
            $model_userinformations = new Model_DbTable_UtilisateurInformations;
            $model_groupe = new Model_DbTable_Groupe;
            $model_fonction = new Model_DbTable_Fonction;
            $model_preferences = new Model_DbTable_UtilisateurPreferences;
            
            $user = $model_user->find($id_user)->current()->toArray();
            $user_groupements = $model_user->getGroupements($user['ID_UTILISATEUR']);
            $user_commissions = $model_user->getCommissions($user['ID_UTILISATEUR']);
            
            $user = array_merge($user, array('uid' => $user['ID_UTILISATEUR']));
            $user = array_merge($user, array('infos' => $model_userinformations->find($user['ID_UTILISATEURINFORMATIONS'])->current()->toArray()));
            $user = array_merge($user, array('group' => $model_groupe->find($user['ID_GROUPE'])->current()->toArray()));
            $user = array_merge($user, array('preferences' => $model_preferences->fetchRow('ID_UTILISATEUR = '.$user['ID_UTILISATEUR'])->toArray()));
            $user = array_merge($user, array('groupements' => $user_groupements == null ? null : $user_groupements->toArray()));
            $user = array_merge($user, array('commissions' => $user_commissions == null ? null : $user_commissions->toArray()));
            $user['infos'] = array_merge($user['infos'], array('LIBELLE_FONCTION' => $model_fonction->find($user['infos']['ID_FONCTION'])->current()->toArray()['LIBELLE_FONCTION']));


            $cache->save(serialize($user));
        }

        return $user;
    }

    

    /**
     * Récupération d'un utilisateur via son nom d'utilisateur
     *
     * @param string $username
     * @return array|null
     */
    public function findByUsername($username)
    {
        $model_user = new Model_DbTable_Utilisateur;
        $user = $model_user->fetchRow($model_user->select()->where('USERNAME_UTILISATEUR = ?', $username));
        return $user !== null ? $this->find($user->ID_UTILISATEUR) : null;
    }

    /**
     * Mise à jour de la dernière action de l'utilisateur
     *
     * @param int $id_user
     * @param string $last_action_date Par défaut date("Y:m-d H:i:s")
     */
    public function updateLastActionDate($id_user, $last_action_date = '')
    {
        $model_user = new Model_DbTable_Utilisateur;
        $user = $model_user->find($id_user)->current();
        $user->LASTACTION_UTILISATEUR = $last_action_date == '' ? date("Y:m-d H:i:s") : $last_action_date;
        $user->save();
    }

    /**
     * Récupération des groupes d'utilisateurs
     *
     * @return array
     */
    public function getAllGroupes()
    {
        $DB_groupe = new Model_DbTable_Groupe;
        return $DB_groupe->fetchAll()->toArray();
    }

    /**
     * Récupération de toutes les fonctions des utilisateurs
     *
     * @return array
     */
    public function getAllFonctions()
    {
        $DB_fonction = new Model_DbTable_Fonction();
        return $DB_fonction->fetchAll()->toArray();
    }

    /**
     * Sauvegarde d'un utilisateur
     *
     * @param array $data
     * @param array $avatar optionnel
     * @param int $id_user optionnel
     * @return int Id de l'utilisateur édité
     */
    public function save($data, $avatar = null, $id_user = null)
    {
        $DB_user = new Model_DbTable_Utilisateur;
        $DB_informations = new Model_DbTable_UtilisateurInformations;
        $DB_groupementsUser = new Model_DbTable_UtilisateurGroupement;
        $DB_commissionsUser = new Model_DbTable_UtilisateurCommission;
        $DB_userPreferences = new Model_DbTable_UtilisateurPreferences;
        
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            if ($DB_user->isRegistered($id_user, $data['USERNAME_UTILISATEUR'])) {
                throw new Exception("Nom d'utilisateur déjà enregistré. Veuillez en choisir un autre.");
            }
            $user = $id_user == null ? $DB_user->createRow() : $DB_user->find($id_user)->current();
            $informations = $id_user == null ? $DB_informations->createRow() : $DB_informations->find($user->ID_UTILISATEURINFORMATIONS)->current();
                
            $informations->NOM_UTILISATEURINFORMATIONS = $data['NOM_UTILISATEURINFORMATIONS'];
            $informations->PRENOM_UTILISATEURINFORMATIONS = $data['PRENOM_UTILISATEURINFORMATIONS'];
            $informations->GRADE_UTILISATEURINFORMATIONS = $data['GRADE_UTILISATEURINFORMATIONS'];
            $informations->TELFIXE_UTILISATEURINFORMATIONS = $data['TELFIXE_UTILISATEURINFORMATIONS'];
            $informations->TELPORTABLE_UTILISATEURINFORMATIONS = $data['TELPORTABLE_UTILISATEURINFORMATIONS'];
            $informations->TELFAX_UTILISATEURINFORMATIONS = $data['TELFAX_UTILISATEURINFORMATIONS'];
            $informations->MAIL_UTILISATEURINFORMATIONS = $data['MAIL_UTILISATEURINFORMATIONS'];
            $informations->WEB_UTILISATEURINFORMATIONS = $data['WEB_UTILISATEURINFORMATIONS'];
            $informations->OBS_UTILISATEURINFORMATIONS = $data['OBS_UTILISATEURINFORMATIONS'];
            $informations->ID_FONCTION = $data['ID_FONCTION'];

            $informations->save();

            $user->USERNAME_UTILISATEUR = $data['USERNAME_UTILISATEUR'];
            $user->NUMINSEE_COMMUNE = array_key_exists('NUMINSEE_COMMUNE', $data) ? $data['NUMINSEE_COMMUNE'] : null;
            $user->ID_GROUPE = $data['ID_GROUPE'];
            $user->ACTIF_UTILISATEUR = $data['ACTIF_UTILISATEUR'];
            $user->FAILED_LOGIN_ATTEMPTS_UTILISATEUR = $data['FAILED_LOGIN_ATTEMPTS_UTILISATEUR'];
            $user->IP_UTILISATEUR = $data['IP_UTILISATEUR'];
            $user->ID_UTILISATEURINFORMATIONS = $informations->ID_UTILISATEURINFORMATIONS;

            if(array_key_exists('PASSWD_INPUT', $data)) {
                if (getenv('PREVARISC_ENFORCE_SECURITY') == 1 && $data['PASSWD_INPUT'] != '') {
                    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W)[a-zA-Z\d\W]{8,}$/', $data['PASSWD_INPUT']))
                        throw new Exception("Votre mot de passe doit contenir au moins 8 caractères "
                            . "dont 1 minuscule, 1 majuscule, 1 chiffre et 1 caractère spécial.");
                }
                $user->PASSWD_UTILISATEUR = $data['PASSWD_INPUT'] == '' ? null : md5($user->USERNAME_UTILISATEUR . getenv('PREVARISC_SECURITY_SALT') . $data['PASSWD_INPUT']);
            }

            $user->save();

            $DB_groupementsUser->delete("ID_UTILISATEUR = " . $user->ID_UTILISATEUR);
            $DB_commissionsUser->delete("ID_UTILISATEUR = " . $user->ID_UTILISATEUR);
            
            if ($id_user == null) {
                $userPreferences = $DB_userPreferences->createRow();
                $userPreferences->ID_UTILISATEUR = $user->ID_UTILISATEUR;
                $userPreferences->save();
            }
            
            if(array_key_exists('commissions', $data)) {
                foreach($data["commissions"] as $id) {
                    $row = $DB_commissionsUser->createRow();
                    $row->ID_UTILISATEUR = $user->ID_UTILISATEUR;
                    $row->ID_COMMISSION = $id;
                    $row->save();
                }
            }

            if(array_key_exists('groupements', $data)) {
                foreach($data["groupements"] as $id) {
                    $row = $DB_groupementsUser->createRow();
                    $row->ID_UTILISATEUR = $user->ID_UTILISATEUR;
                    $row->ID_GROUPEMENT = $id;
                    $row->save();
                }
            }

            Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cacheSearch')->clean(Zend_Cache::CLEANING_MODE_ALL);
            $db->commit();

            $cache->remove('user_id_' . $user->ID_UTILISATEUR);

            // Gestion de l'avatar
            if($avatar !== null && isset($avatar["tmp_name"]) && is_file($avatar["tmp_name"])) {

                $path = REAL_DATA_PATH . DS . 'uploads' . DS . 'avatars' . DS;
                $extension = strtolower(strrchr($avatar['name'], "."));

                GD_Resize::run($avatar["tmp_name"], $path . "small" . DS . $user->ID_UTILISATEUR . ".jpg", 25, 25);
                GD_Resize::run($avatar["tmp_name"], $path . "medium" . DS . $user->ID_UTILISATEUR . ".jpg", 150);
                GD_Resize::run($avatar["tmp_name"], $path . "large" . DS . $user->ID_UTILISATEUR . ".jpg", 224);
            }

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $user->ID_UTILISATEUR;
    }
    
    /**
     * Update users preferences
     * 
     * @param int $id_utilisateur
     * @param array $preferences array of preferences
     * @return mixed false if failed or Model_DbTable_UtilisateurPreferences on success
     */
    public function savePreferences($id_utilisateur, array $preferences = array()) {
        
        if (!$id_utilisateur) {
            return false;
        }
        
        $DB_userPreferences = new Model_DbTable_UtilisateurPreferences;
        $DB_preferences = $DB_userPreferences->fetchRow(array('ID_UTILISATEUR = ?' => $id_utilisateur));
        
        if (!$DB_preferences)  {
            return false;
        }
        
        foreach($preferences as $name => $preference) {
            switch($name) {
                case 'DASHBOARD_BLOCS':
                    $DB_preferences->DASHBOARD_BLOCS = json_encode($preference);
                    break;
                default:
                    $DB_preferences->$name = $preference;
                    break;
            }
        }
        
        $DB_preferences->save();
        
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $cache->remove('user_id_' . $id_utilisateur);
        
        return $DB_preferences;
        
    }

    /**
     * Récupération d'un groupe
     *
     * @param int $id_group
     * @return array
     */
    public function getGroup($id_group)
    {
        $model_groupe = new Model_DbTable_Groupe;
        return $model_groupe->find($id_group)->current()->toArray();
    }

    /**
     * Sauvegarde d'un groupe
     *
     * @param array $data
     * @param int $id_group Optionnel
     * @return int
     */
    public function saveGroup($data, $id_group = null)
    {
        $model_groupe = new Model_DbTable_Groupe;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $group = $id_group == null ? $model_groupe->createRow() : $model_groupe->find($id_group)->current();
            $group->LIBELLE_GROUPE = $data['LIBELLE_GROUPE'];
            $group->DESC_GROUPE = $data['DESC_GROUPE'];
            $group->save();
            $db->commit();

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        
        // if group exists, remove the cache
        if ($id_group) {
            $model_user = new Model_DbTable_Utilisateur;
            $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
            $users = $model_user->fetchAll('ID_GROUPE = '.$id_group);
            foreach($users as $user) {
                $cache->remove('user_id_'.$user->ID_UTILISATEUR);
            }
            $cache->remove('acl');
        }
        
        
        
        return $group->ID_GROUPE;
    }

    /**
     * Suppression d'un groupe
     *
     * @param int $id_group
     */
    public function deleteGroup($id_group)
    {
        $DB_user = new Model_DbTable_Utilisateur;
        $DB_groupe = new Model_DbTable_Groupe;

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        if ($id_group > 1) {

            try {
                // récupération de tous les utilisateurs du groupe à supprimer
                $all_users = $DB_user->fetchAll("ID_GROUPE = " . $id_group);
                $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
                
                // On bouge les users du groupe à supprimer dans le groupe par défaut
                if ($all_users != null) {
                    foreach ($all_users as $item) {
                        $user = $DB_user->find( $item->ID_UTILISATEUR )->current();
                        $user->ID_GROUPE = 1;
                        $user->save();
                        
                        $cache->remove('user_id_'.$item->ID_UTILISATEUR);
                    }
                }

                $DB_groupe->delete($id_group);

                $db->commit();

            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
    }

    /**
     * Récupération des ressources / privilèges associés à un utilisateur
     *
     * @param array $user
     * @return array
     */
    public function getGroupPrivileges($user)
    {
        $model_user = new Model_DbTable_Utilisateur;
        $privileges = $model_user->getGroupPrivileges($user);
        return $privileges;
    }

    /**
     * Récupération des utilisateurs pour les alertes
     * 
     * @param  int      $idChangement  L'id du type changement
     * @param  array    $etablissement L'établissement concerné par le changement
     * @return array    La liste des utilisateurs
     */
    public function getUtilisateursForAlterte($idChangement, $etablissement)
    {
        $dbUtilisateur = new Model_DbTable_Utilisateur;

        return $dbUtilisateur->findUtilisateursForAlerte($idChangement, $etablissement);
    }
    
    /**
     * Log les problèmes de login à un compte utilisateur
     * @param array $user array définissant l'utilisateur
     */
    public function logFailedLogin($user) {
        
        if (!$user || !isset($user['ID_UTILISATEUR']) || !isset($user['FAILED_LOGIN_ATTEMPTS_UTILISATEUR'])) {
            return ;
        }
        
        $dbUtilisateur = new Model_DbTable_Utilisateur;
        $dbUser = $dbUtilisateur->find($user['ID_UTILISATEUR']);
        if (!$dbUser) return ;
        $dbUser = $dbUser->current();
        $dbUser->FAILED_LOGIN_ATTEMPTS_UTILISATEUR++;
        $dbUser->save();
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $cache->remove('user_id_' . $user['ID_UTILISATEUR']);
        $user['FAILED_LOGIN_ATTEMPTS_UTILISATEUR']++;
        return $user;
    }
    
    public function resetFailedLogin($user) {
        if (!$user 
                || !isset($user['ID_UTILISATEUR']) 
                || !isset($user['FAILED_LOGIN_ATTEMPTS_UTILISATEUR'])
        ) {
            return ;
        }
        $dbUtilisateur = new Model_DbTable_Utilisateur;
        $dbUser = $dbUtilisateur->find($user['ID_UTILISATEUR']);
        if (!$dbUser) return ;
        $dbUser = $dbUser->current();
        $dbUser->FAILED_LOGIN_ATTEMPTS_UTILISATEUR = 0;
        $dbUser->IP_UTILISATEUR = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        $dbUser->save();
        $cache = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('cache');
        $cache->remove('user_id_' . $user['ID_UTILISATEUR']);
        $user['FAILED_LOGIN_ATTEMPTS_UTILISATEUR'] = 0;
        $user['IP_UTILISATEUR'] = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        return $user;
    }
    
}
