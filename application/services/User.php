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
        $model_user = new Model_DbTable_Utilisateur;
        $model_userinformations = new Model_DbTable_UtilisateurInformations;
        $model_groupe = new Model_DbTable_Groupe;
        $model_fonction = new Model_DbTable_Fonction;

        $user = $model_user->find($id_user)->current()->toArray();
        $user = array_merge($user, array('uid' => $user['ID_UTILISATEUR']));
        $user = array_merge($user, array('infos' => $model_userinformations->find($user['ID_UTILISATEURINFORMATIONS'])->current()->toArray()));
        $user = array_merge($user, array('group' => $model_groupe->find($user['ID_GROUPE'])->current()->toArray()));
        $user = array_merge($user, array('groupements' => $model_user->getGroupements($user['ID_UTILISATEUR'])->current() == null ? null : $model_user->getGroupements($user['ID_UTILISATEUR'])->current()->toArray()));
        $user = array_merge($user, array('commissions' => $model_user->getCommissions($user['ID_UTILISATEUR'])->current() == null ? null : $model_user->getCommissions($user['ID_UTILISATEUR'])->current()->toArray()));
        $user['infos'] = array_merge($user['infos'], array('LIBELLE_FONCTION' => $model_fonction->find($user['infos']['ID_FONCTION'])->current()->toArray()['LIBELLE_FONCTION']));
        return $user;
    }

    /**
     * Récupération d'un utilisateur
     *
     * @param int $id_user
     * @return array
     */
    public function getEtablissements($id_user)
    {
        // Etablissements liés
        $etablissements = array();

        // Ets 1 - 4ème catégorie
        $search = new Model_DbTable_Search;
        $search->setItem("etablissement");
        $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
        $search->setCriteria("etablissementinformations.ID_CATEGORIE", array("1","2","3","4"));
        $search->setCriteria("etablissementinformations.ID_GENRE", 2);
        $etablissements = array_merge($search->run(null, null, false)->toArray(), $etablissements);

        // 5ème catégorie defavorable
        $search = new Model_DbTable_Search;
        $search->setItem("etablissement");
        $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
        $search->setCriteria("etablissementinformations.ID_CATEGORIE", "5");
        $search->setCriteria("avis.ID_AVIS", 2);
        $search->setCriteria("etablissementinformations.ID_GENRE", 2);
        $etablissements = array_merge($search->run(null, null, false)->toArray(), $etablissements);

        // 5ème catégorie avec local à sommeil
        $search = new Model_DbTable_Search;
        $search->setItem("etablissement");
        $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
        $search->setCriteria("etablissementinformations.ID_CATEGORIE", "5");
        $search->setCriteria("etablissementinformations.ID_GENRE", 2);
        $search->setCriteria("etablissementinformations.LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS", "1");
        $etablissements = array_merge($search->run(null, null, false)->toArray(), $etablissements);

        // EIC - IGH - HAB
        $search = new Model_DbTable_Search;
        $search->setItem("etablissement");
        $search->setCriteria("utilisateur.ID_UTILISATEUR", $id_user);
        $search->setCriteria("etablissementinformations.ID_GENRE", array("6","5","4"));
        $etablissements = array_merge($search->run(null, null, false)->toArray(), $etablissements);

        return $etablissements;
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

        $db = Zend_Db_Table::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $user = $id_user == null ? $DB_user->createRow() : $DB_user->find($id_user)->current();
            $informations = $id_user == null ? $DB_informations->createRow() : $DB_informations->find($user->ID_UTILISATEURINFORMATIONS)->current();

            $informations->NOM_UTILISATEURINFORMATIONS = $data['NOM_UTILISATEURINFORMATIONS'];
            $informations->PRENOM_UTILISATEURINFORMATIONS = $data['PRENOM_UTILISATEURINFORMATIONS'];
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
            $user->ID_UTILISATEURINFORMATIONS = $informations->ID_UTILISATEURINFORMATIONS;

            if(array_key_exists('PASSWD_INPUT', $data)) {
                $user->PASSWD_UTILISATEUR = $data['PASSWD_INPUT'] == '' ? null : md5($user->USERNAME_UTILISATEUR . Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOption('security')['salt'] . $data['PASSWD_INPUT']);
            }

            $user->save();

            $DB_groupementsUser->delete("ID_UTILISATEUR = " . $user->ID_UTILISATEUR);
            $DB_commissionsUser->delete("ID_UTILISATEUR = " . $user->ID_UTILISATEUR);

            if(array_key_exists('commissions', $data)) {
                foreach($data["commissions"] as $id) {
                    $row = $DB_groupementsUser->createRow();
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

            // Gestion de l'avatar
            if($avatar !== null) {

                $path = APPLICATION_PATH . DS . '..' . DS . 'public' . DS . 'data' . DS . 'uploads' . DS . 'avatars' . DS;
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

                // On bouge les users du groupe à supprimer dans le groupe par défaut
                if ($all_users != null) {
                    foreach ($all_users as $item) {
                        $user = $DB_user->find( $item->ID_UTILISATEUR )->current();
                        $user->ID_GROUPE = 1;
                        $user->save();
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
}
