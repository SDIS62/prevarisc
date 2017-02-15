<?php
    class Model_DbTable_Utilisateur extends Zend_Db_Table_Abstract
    {
        protected $_name="utilisateur";
        protected $_primary = "ID_UTILISATEUR";

        public function getDroits($id_user)
        {
            $auth = Zend_Auth::getInstance()->getIdentity();

            // modèle
            $model_groupes = new Model_DbTable_Groupe;

            // Récupération du groupe de l'user
            if($auth->ID_UTILISATEUR != $id_user)
                $id_groupe = $this->find($id_user)->current()->ID_GROUPE;
            else
                $id_groupe = $auth->ID_GROUPE;

            // On retourne les droits de l'user
            return $model_groupes->getDroits($id_groupe);
        }

        public function getUsersWithInformations($group = null)
        {
            $this->select = $this->select()->setIntegrityCheck(false);
            $select = $this	 ->select
                             ->from(array("u" => "utilisateur"), array(
                                 "uid" => "ID_UTILISATEUR",
                                 "ID_UTILISATEUR",
                                 "USERNAME_UTILISATEUR",
                                 "PASSWD_UTILISATEUR",
                                 "ID_UTILISATEURINFORMATIONS",
                                 "ACTIF_UTILISATEUR",
                                 "ID_GROUPE",
                                 "LASTACTION_UTILISATEUR"
                            ))
                             ->join("utilisateurinformations", "u.ID_UTILISATEURINFORMATIONS = utilisateurinformations.ID_UTILISATEURINFORMATIONS")
                             ->join("fonction", "utilisateurinformations.ID_FONCTION = fonction.ID_FONCTION")
                             ->order("utilisateurinformations.NOM_UTILISATEURINFORMATIONS ASC");

            if( !empty($group) )
                $select->where("ID_GROUPE = $group");

            return $this->fetchAll($select)->toArray();
        }

        public function isRegistered($id_user, $login)
        {
            $select = $this->select()
                ->from("utilisateur")
                ->where("USERNAME_UTILISATEUR = ?", $login);
                if ($id_user !== null) {
                    $select->where("ID_UTILISATEUR <> ?", $id_user);
                }
                $select->limit(1);

            $result = $this->fetchRow($select);

            return $result != null;
        }

        public function getId($login)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("utilisateur", "ID_UTILISATEUR")
                ->where("USERNAME_UTILISATEUR = ?", $login)
                ->limit(1);

            $result = $this->fetchRow($select);

            return ( $result != null ) ? $result->ID_UTILISATEUR : null;
        }

        public function getCommissions($id)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("utilisateurcommission", null)
                ->join("commission", "commission.ID_COMMISSION = utilisateurcommission.ID_COMMISSION")
                ->join("commissiontype", "commission.ID_COMMISSIONTYPE = commissiontype.ID_COMMISSIONTYPE")
                ->where("ID_UTILISATEUR = ?", $id);

            return $this->fetchAll($select);
        }

        public function getCommissionsArray($id)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("utilisateurcommission", "ID_COMMISSION")
                ->where("ID_UTILISATEUR = ?", $id);

            $all = $this->fetchAll($select);

            if( $all == null)

                return array();

            $all = $all->toArray();
            $result = array();
            foreach ($all as $row) {
                $result[] = $row["ID_COMMISSION"];
            }

            return $result;
        }

        public function getGroupements($id)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("utilisateurgroupement", null)
                ->join("groupement", "groupement.ID_GROUPEMENT = utilisateurgroupement.ID_GROUPEMENT")
                ->where("ID_UTILISATEUR = ?", $id);

            return $this->fetchAll($select);
        }

        public function getVillesDeSesGroupements($id)
        {
            $model_groupementCommune = new Model_DbTable_GroupementCommune;

            $rowset_groupements = $this->getGroupements($id);

            $villes = array();

            // pr chq gpt on prend ses ville qu'on met ds un tableau
            foreach ($rowset_groupements as $row_groupement) {

                foreach ($model_groupementCommune->find($row_groupement->ID_GROUPEMENT) as $row) {
                    $villes[] = $row->NUMINSEE_COMMUNE;
                }
            }

            // on enlève les doublons
            $villes = array_unique($villes);

            // on envoit
            return $villes;
        }

        public function getGroupPrivileges($user)
        {
            // Récupération des données utilisateur

            $group = $user['group']['ID_GROUPE'];

            $groupements = (array) $user['groupements'];
            array_walk($groupements, function(&$val, $key) use(&$groupements){ $val = $groupements[$key]['ID_GROUPEMENT']; });
            $groupements = implode('-', $groupements);

            $commissions = (array) $user['commissions'];
            array_walk($commissions, function(&$val, $key) use(&$commissions){ $val = $commissions[$key]['ID_COMMISSION']; });
            $commissions = implode('-', $commissions);

            $commune = $user['NUMINSEE_COMMUNE'];

            // Récupération depuis la base des ressources / privileges du groupe de l'utilisateur

            $this->select = $this->select()->setIntegrityCheck(false);
            $select = $this->select->from("groupe-privileges", null)
                ->join('privileges', '`groupe-privileges`.id_privilege = privileges.id_privilege', array('name_privilege' => 'name'))
                ->join('resources', 'privileges.id_resource = resources.id_resource', array('name_resource' => 'name'))
                ->where('`groupe-privileges`.ID_GROUPE = ?', $group);

            $privileges = $this->fetchAll($select)->toArray();

            // On créé une fonction spéciale pour convertir les ressources retravaillées

            $develop_resources = function(&$list_resources_finale) use (&$develop_resources) {
                for($i = 0; $i < count($list_resources_finale); $i++) {
                    $resource_exploded = explode('_', $list_resources_finale[$i]);
                    for($j = 0; $j < count($resource_exploded); $j++) {
                        if(count(explode('-', $resource_exploded[$j])) > 1) {
                            $resource_exploded2 = explode('-', $resource_exploded[$j]);
                            for($k = 0; $k < count($resource_exploded2); $k++) {
                                $name = explode('_', $list_resources_finale[$i]);
                                $name[$j] = $resource_exploded2[$k];
                                $list_resources_finale[] = implode($name, '_');
                            }
                            unset($list_resources_finale[$i]);
                            $list_resources_finale = array_unique($list_resources_finale);
                            $list_resources_finale = array_values($list_resources_finale);
                            $develop_resources($list_resources_finale);
                        }
                    }
                }
                return array_unique($list_resources_finale);
            };

            // Spécialisation des ressources pour l'utilisateur

            foreach($privileges as $key => $resource) {
                if(explode('_', $resource['name_resource'])[0] == 'etablissement') {

                    $resource_exploded = explode('_', $resource['name_resource']);

                    switch($resource_exploded[1]) {
                        case 'erp':
                            if($resource_exploded[4] == '1') $resource_exploded[4] = $commissions;
                            if($resource_exploded[5] == '1') $resource_exploded[5] = $groupements;
                            if($resource_exploded[6] == '1') $resource_exploded[6] = $commune;
                            break;
                        case 'hab':
                            if($resource_exploded[3] == '1') $resource_exploded[3] = $groupements;
                            if($resource_exploded[4] == '1') $resource_exploded[4] = $commune;
                            break;
                        case 'igh':
                            if($resource_exploded[3] == '1') $resource_exploded[3] = $commissions;
                            if($resource_exploded[4] == '1') $resource_exploded[4] = $groupements;
                            if($resource_exploded[5] == '1') $resource_exploded[5] = $commune;
                            break;
                        case 'eic':
                            if($resource_exploded[2] == '1') $resource_exploded[2] = $groupements;
                            if($resource_exploded[3] == '1') $resource_exploded[3] = $commune;
                            break;
                        case 'camp':
                            if($resource_exploded[2] == '1') $resource_exploded[2] = $groupements;
                            if($resource_exploded[3] == '1') $resource_exploded[3] = $commune;
                            break;
                        case 'temp':
                            if($resource_exploded[2] == '1') $resource_exploded[2] = $groupements;
                            if($resource_exploded[3] == '1') $resource_exploded[3] = $commune;
                            break;
                        case 'iop':
                            if($resource_exploded[2] == '1') $resource_exploded[2] = $groupements;
                            if($resource_exploded[3] == '1') $resource_exploded[3] = $commune;
                            break;
                        case 'zone':
                            if($resource_exploded[3] == '1') $resource_exploded[3] = $groupements;
                            if($resource_exploded[4] == '1') $resource_exploded[4] = $commune;
                            break;
                    }

                    $resource_imploded = implode($resource_exploded, '_');
                    $resource_tmp = array($resource_imploded);
                    $develop_resources($resource_tmp);

                    array_push($privileges, array('name_privilege' => $resource['name_privilege'], 'name_resource' => $resource_imploded));

                    unset($privileges[$key]);
                }
            }

            return $privileges;
        }

        /**
         * Retourne une liste d'utilisateur ayant les droits
         * de recevoir les type d'alerte (changement de statut, avis, catégorie)
         * et étant concerné par la commune ou le groupement de commune de l'établissement
         * 
         * @param  int      $idChangement   L'id du type de changement
         * @param  array    $ets            L'établissement concerné par le changement
         * @return array                    Liste d'utilisateur
         */
        public function findUtilisateursForAlerte($idChangement, $ets)
        {
            switch($idChangement) {
                case "1":
                    $privilege = "alerte_statut";
                    break;
                case "2":
                    $privilege = "alerte_avis";
                    break;
                case "3":
                    $privilege = "alerte_classement";
                    break;
                default:
                    $privilege = "alerte_statut";
            }

            $numinsee = "";
            if (count($ets['adresses']) > 0) {
                $numinsee = $ets['adresses'][0]['NUMINSEE_COMMUNE'];
            }

            $selectPrivilegeQuery = $this->select()->setIntegrityCheck(false)
                                         ->from(array('p' => 'privileges'), array('p.id_privilege'))
                                         ->where('name = ?', $privilege)
                                         ->limit(1);

            $selectCommune = $this->select()->setIntegrityCheck(false)
                                  ->from(array('u' => 'utilisateur'), array('ID_UTILISATEUR'))
                                  ->join(array('ui' => 'utilisateurinformations'), 
                                         'ui.ID_UTILISATEURINFORMATIONS = u.ID_UTILISATEURINFORMATIONS',
                                         array('ui.NOM_UTILISATEURINFORMATIONS', 'ui.PRENOM_UTILISATEURINFORMATIONS',
                                               'ui.MAIL_UTILISATEURINFORMATIONS'))
                                  ->join(array('g' => 'groupe'), 'g.ID_GROUPE = u.ID_GROUPE', null)
                                  ->join(array('gp' => 'groupe-privileges'), 'gp.ID_GROUPE = g.ID_GROUPE', null)
                                  ->where('ui.MAIL_UTILISATEURINFORMATIONS IS NOT NULL')
                                  ->where('ui.MAIL_UTILISATEURINFORMATIONS <> ?', '')
                                  ->where('gp.id_privilege = (' . $selectPrivilegeQuery . ')')
                                  ->where('u.NUMINSEE_COMMUNE = ?', $numinsee)
                                  ->group('u.ID_UTILISATEUR');

            $selectGroupement = $this->select()->setIntegrityCheck(false)
                                     ->from(array('u' => 'utilisateur'), array('ID_UTILISATEUR'))
                                     ->join(array('ui' => 'utilisateurinformations'), 
                                            'ui.ID_UTILISATEURINFORMATIONS = u.ID_UTILISATEURINFORMATIONS',
                                            array('ui.NOM_UTILISATEURINFORMATIONS', 'ui.PRENOM_UTILISATEURINFORMATIONS',
                                                  'ui.MAIL_UTILISATEURINFORMATIONS'))
                                     ->join(array('g' => 'groupe'), 'g.ID_GROUPE = u.ID_GROUPE', null)
                                     ->join(array('gp' => 'groupe-privileges'), 'gp.ID_GROUPE = g.ID_GROUPE', null)
                                     ->join(array('ug' => 'utilisateurgroupement'), 'ug.ID_UTILISATEUR = u.ID_UTILISATEUR', null)
                                     ->join(array('gc' => 'groupementcommune'), 'gc.ID_GROUPEMENT = ug.ID_GROUPEMENT', null)
                                     ->where('ui.MAIL_UTILISATEURINFORMATIONS IS NOT NULL')
                                     ->where('ui.MAIL_UTILISATEURINFORMATIONS <> ?', '')
                                     ->where('gp.id_privilege = (' . $selectPrivilegeQuery . ')')
                                     ->where('gc.NUMINSEE_COMMUNE = ?', $numinsee)
                                     ->group('u.ID_UTILISATEUR');

             $selectUnion = $this->select()
                                 ->union(array($selectCommune, $selectGroupement));


            return $this->fetchAll($selectUnion)->toArray();
        }

    }
