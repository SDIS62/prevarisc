<?php
    class Model_DbTable_Utilisateur extends Zend_Db_Table_Abstract
    {
        protected $_name="utilisateur";
        protected $_primary = "ID_UTILISATEUR";

        public function isRegistered($login)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("utilisateur")
                ->where("USERNAME_UTILISATEUR = ?", $login)
                ->limit(1);

            $result = $this->fetchRow($select);

            return ( $result != null ) ? true : false;
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

        public function getGroupements($id)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("utilisateurgroupement", null)
                ->join("groupement", "groupement.ID_GROUPEMENT = utilisateurgroupement.ID_GROUPEMENT")
                ->where("ID_UTILISATEUR = ?", $id);

            return $this->fetchAll($select);
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

    }
