<?php
    class Model_DbTable_UtilisateurInformations extends Zend_Db_Table_Abstract
    {
        protected $_name="utilisateurinformations"; // Nom de la base
        protected $_primary = "ID_UTILISATEURINFORMATIONS"; // Cl� primaire

        public function getContact($item, $id)
        {
            $select = $this->select()->setIntegrityCheck(false);

            // Initalisation des modèles
            switch ($item) {
                case "etablissement":
                    $select  ->from("etablissementcontact", null)
                             ->join("utilisateurinformations", "utilisateurinformations.ID_UTILISATEURINFORMATIONS = etablissementcontact.ID_UTILISATEURINFORMATIONS")
                             ->join("fonction", "utilisateurinformations.ID_FONCTION = fonction.ID_FONCTION", "LIBELLE_FONCTION")
                             ->where("etablissementcontact.ID_ETABLISSEMENT = '$id'")
                             ->order("utilisateurinformations.NOM_UTILISATEURINFORMATIONS ASC");
                    break;
                case "dossier":
                    $select  ->from("dossiercontact", null)
                             ->join("utilisateurinformations", "utilisateurinformations.ID_UTILISATEURINFORMATIONS = dossiercontact.ID_UTILISATEURINFORMATIONS")
                             ->join("fonction", "utilisateurinformations.ID_FONCTION = fonction.ID_FONCTION", "LIBELLE_FONCTION")
                             ->where("dossiercontact.ID_DOSSIER = '$id'")
                             ->order("utilisateurinformations.NOM_UTILISATEURINFORMATIONS ASC");
                    break;
                case "groupement":
                    $select  ->from("groupementcontact", null)
                             ->join("utilisateurinformations", "utilisateurinformations.ID_UTILISATEURINFORMATIONS = groupementcontact.ID_UTILISATEURINFORMATIONS")
                             ->join("fonction", "utilisateurinformations.ID_FONCTION = fonction.ID_FONCTION", "LIBELLE_FONCTION")
                             ->where("groupementcontact.ID_GROUPEMENT = '$id'")
                             ->order("utilisateurinformations.NOM_UTILISATEURINFORMATIONS ASC");
                    break;
                case "commission":
                    $select  ->from("commissioncontact", null)
                             ->join("utilisateurinformations", "utilisateurinformations.ID_UTILISATEURINFORMATIONS = commissioncontact.ID_UTILISATEURINFORMATIONS")
                             ->join("fonction", "utilisateurinformations.ID_FONCTION = fonction.ID_FONCTION", "LIBELLE_FONCTION")
                             ->where("commissioncontact.ID_COMMISSION = '$id'")
                             ->order("utilisateurinformations.NOM_UTILISATEURINFORMATIONS ASC");
                    break;
            }

            return $this->fetchAll($select)->toArray();
        }

        public function getAllContacts($name)
        {
            $q = "%$name%";
            $select = $this->select()->setIntegrityCheck(false);
            $select  ->from("utilisateurinformations")
                     ->where("NOM_UTILISATEURINFORMATIONS LIKE ? OR PRENOM_UTILISATEURINFORMATIONS LIKE ? OR SOCIETE_UTILISATEURINFORMATIONS LIKE ?",$q,$q,$q)
                     ->where("ID_UTILISATEURINFORMATIONS NOT IN (SELECT ID_UTILISATEURINFORMATIONS FROM utilisateur)");

            return ( $this->fetchAll( $select ) != null ) ? $this->fetchAll( $select )->toArray() : null;
        }
		
		

    }
