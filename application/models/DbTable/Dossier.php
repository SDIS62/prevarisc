<?php

    class Model_DbTable_Dossier extends Zend_Db_Table_Abstract
    {

        protected $_name="dossier"; // Nom de la base
        protected $_primary = "ID_DOSSIER"; // Cl� primaire

        //Fonction qui récup tous les établissements liés au dossier LAST VERSION
        public function getEtablissementDossier($id_dossier)
        {

			//retourne la liste des catégories de prescriptions par ordre
            $select = "
                SELECT etablissementdossier.ID_ETABLISSEMENTDOSSIER ,t1.ID_ETABLISSEMENT, LIBELLE_ETABLISSEMENTINFORMATIONS, LIBELLE_GENRE
                FROM etablissementdossier, etablissementinformations t1, genre
                WHERE etablissementdossier.ID_ETABLISSEMENT = t1.ID_ETABLISSEMENT
                AND t1.ID_GENRE = genre.ID_GENRE
                AND etablissementdossier.ID_DOSSIER = '".$id_dossier."'
                AND t1.DATE_ETABLISSEMENTINFORMATIONS = (
                    SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS)
                    FROM etablissementdossier, etablissementinformations
                    WHERE etablissementinformations.ID_ETABLISSEMENT = t1.ID_ETABLISSEMENT
                )
				GROUP BY ID_ETABLISSEMENT;
            ";
            //echo $select;
            return $this->getAdapter()->fetchAll($select);
        }

        // Fonction optimisée pour les ACL
        public function getEtablissementDossier2($id_dossier)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("etablissementdossier", array("etablissementdossier.ID_ETABLISSEMENT"))
                ->where("etablissementdossier.ID_DOSSIER = ?", $id_dossier);

            return $this->fetchAll($select)->toArray();
        }

        //retourne 1 si dossier Etude - 0 si Visite
        public function getTypeDossier($id_dossier)
        {
			$select = $this->select()
                ->setIntegrityCheck(false)
                ->from("dossier", "TYPE_DOSSIER")
                ->where("dossier.ID_DOSSIER = ?",$id_dossier);

            return $this->getAdapter()->fetchRow($select);
        }

        public function getNatureDossier($id_dossier)
        {
            $select = "SELECT ID_NATURE
            FROM dossiernature
            WHERE id_dossier = '".$id_dossier."';";

            //echo $select;
            return $this->getAdapter()->fetchRow($select);
        }

        public function getCommissionV2($idDossier){
             $select = $this->select()
                ->setIntegrityCheck(false)
                ->from(array("d" => "dossier"), "d.ID_DOSSIER")
                ->join(array("c" => "commission") , "d.COMMISSION_DOSSIER = c.ID_COMMISSION")
                ->join(array("ct" => "commissiontype"), "c.ID_COMMISSIONTYPE = ct.ID_COMMISSIONTYPE")
                ->where("d.ID_DOSSIER = ?",$idDossier);

            return $this->getAdapter()->fetchRow($select);
        }

        public function getDossierTypeNature($idDossier)
        {
            $select = "
                SELECT *
                FROM dossier, dossiertype, dossiernature, dossiernatureliste
                WHERE dossier.TYPE_DOSSIER = dossiertype.ID_DOSSIERTYPE
                AND dossier.ID_DOSSIER = dossiernature.ID_DOSSIER
                AND dossiernatureliste.ID_DOSSIERNATURE = dossiernature.ID_NATURE
                AND dossier.id_dossier = '".$idDossier."'
            ";
            //echo $select;
            return $this->getAdapter()->fetchAll($select);
        }

        public function findLastVp($idEtab)
        {
            $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('d' => 'dossier'))
                    ->join(array('ed' => 'etablissementdossier') , "ed.ID_DOSSIER = d.ID_DOSSIER")
                    ->join(array("dn" => "dossiernature") , "d.ID_DOSSIER = dn.ID_DOSSIER")
                    ->where("ed.ID_ETABLISSEMENT = ?",$idEtab)
                    ->where("dn.ID_NATURE = 21 OR dn.ID_NATURE = 26")
                    ->where("d.DATEVISITE_DOSSIER IS NOT NULL")
                    ->order("d.DATEVISITE_DOSSIER desc")
                    ->limit(1);

            //echo $select->__toString();
            return $this->getAdapter()->fetchRow($select);

        }

        public function findLastVpCreationDoc($idEtab,$idDossier,$dateVisite)
        {
            $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('d' => 'dossier'))
                    ->join(array('ed' => 'etablissementdossier') , "ed.ID_DOSSIER = d.ID_DOSSIER")
                    ->join(array("dn" => "dossiernature") , "d.ID_DOSSIER = dn.ID_DOSSIER")
                    ->where("ed.ID_ETABLISSEMENT = ?",$idEtab)
                    ->where("ed.ID_DOSSIER <> ?",$idDossier)
                    ->where("dn.ID_NATURE = 21 OR dn.ID_NATURE = 26")
                    ->where("d.DATEVISITE_DOSSIER IS NOT NULL")
                    ->where("d.DATEVISITE_DOSSIER < ?",$dateVisite)
                    ->order("d.DATEVISITE_DOSSIER desc")
                    ->limit(1);

            //echo $select->__toString();
            return $this->getAdapter()->fetchRow($select);

        }

        public function getAvisDossier($id_dossier)
        {
           $select = $this->select()
				->setIntegrityCheck(false)
				->from(array('a' => 'avis'),"LIBELLE_AVIS")
				->join(array('d' => 'dossier') , "d.AVIS_DOSSIER_COMMISSION = a.ID_AVIS")
				->where("d.ID_DOSSIER = ?",$id_dossier);
            //echo $select;
            return $this->getAdapter()->fetchRow($select);
        }

        public function getEtablissementDossierGenConvoc($id_dossier)
        {
            $select = "
                SELECT etablissementdossier.ID_ETABLISSEMENTDOSSIER ,t1.ID_ETABLISSEMENT, LIBELLE_ETABLISSEMENTINFORMATIONS, LIBELLE_GENRE
                FROM etablissementdossier, etablissementinformations t1, genre
                WHERE etablissementdossier.ID_ETABLISSEMENT = t1.ID_ETABLISSEMENT
                AND t1.ID_GENRE = genre.ID_GENRE
                AND etablissementdossier.ID_DOSSIER = '".$id_dossier."'
                AND t1.DATE_ETABLISSEMENTINFORMATIONS = (
                    SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS)
                    FROM etablissementdossier, etablissementinformations
                    WHERE etablissementinformations.ID_ETABLISSEMENT = t1.ID_ETABLISSEMENT
                )
                GROUP BY ID_ETABLISSEMENT;

            ";

            //echo $select;
            return $this->getAdapter()->fetchAll($select);
        }

        public function listeDesDossierDateCommissionEchu($idsCommission, $sinceDays = 10, $untilDays = 100)
        {
            $ids = (array) $idsCommission;

            $select = $this->select()->setIntegrityCheck(false)
                         ->from(array("d" => "dossier"))
                         ->joinLeft("dossierlie", "d.ID_DOSSIER = dossierlie.ID_DOSSIER2")
                         ->join("dossiernature", "dossiernature.ID_DOSSIER = d.ID_DOSSIER", null)
                         ->join("dossiernatureliste", "dossiernatureliste.ID_DOSSIERNATURE = dossiernature.ID_NATURE", array("LIBELLE_DOSSIERNATURE", "ID_DOSSIERNATURE"))
                         ->join("dossiertype", "dossiertype.ID_DOSSIERTYPE = dossiernatureliste.ID_DOSSIERTYPE", "LIBELLE_DOSSIERTYPE")
                         ->joinLeft("dossierdocurba", "d.ID_DOSSIER = dossierdocurba.ID_DOSSIER", "NUM_DOCURBA")
                         ->joinLeft(array("e" => "etablissementdossier"), "d.ID_DOSSIER = e.ID_DOSSIER", null)
                         ->joinLeft("avis", "d.AVIS_DOSSIER_COMMISSION = avis.ID_AVIS")
                         ->joinLeft("dossierpreventionniste", "dossierpreventionniste.ID_DOSSIER = d.ID_DOSSIER", null)
                         ->joinLeft("utilisateur", "utilisateur.ID_UTILISATEUR = dossierpreventionniste.ID_PREVENTIONNISTE", "ID_UTILISATEUR")
                         ->joinLeft("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT AND etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT )", "LIBELLE_ETABLISSEMENTINFORMATIONS")
                         ->joinLeft("dossieraffectation", "dossieraffectation.ID_DOSSIER_AFFECT = d.ID_DOSSIER")
                         ->joinLeft("datecommission", "dossieraffectation.ID_DATECOMMISSION_AFFECT = datecommission.ID_DATECOMMISSION ")
                         ->group("d.ID_DOSSIER")
                         ->where("DATEDIFF(CURDATE(), datecommission.DATE_COMMISSION) >= ".((int) $sinceDays))
                         ->where("DATEDIFF(CURDATE(), datecommission.DATE_COMMISSION) <= ".((int) $untilDays))
                         ->where("d.AVIS_DOSSIER_COMMISSION IS NULL or d.AVIS_DOSSIER_COMMISSION = 0")
                         ->order("datecommission.DATE_COMMISSION desc");

            if (count($ids) > 0) {
                $select->where("datecommission.COMMISSION_CONCERNE IN (".implode(",", $ids).")");
            }


            return $this->getAdapter()->fetchAll($select);
        }

        public function listeDossierAvecAvisDiffere($idsCommission) {

            $ids = (array) $idsCommission;

            // Dossiers avec avis différé
            $search = new Model_DbTable_Search;
            $search->setItem("dossier");
            if (count($ids) > 0) {
                $search->setCriteria("d.COMMISSION_DOSSIER", $ids);
            }
            $search->setCriteria("d.DIFFEREAVIS_DOSSIER", 1);
            return $search->run(false, null, false)->toArray();
        }

        public function listeDesCourrierSansReponse($duree_en_jour = 5)
        {
            $search = new Model_DbTable_Search;
            $search->setItem("dossier");
            $search->setCriteria("d.TYPE_DOSSIER", 5);
            $search->setCriteria("d.DATEREP_DOSSIER IS NULL");
            $search->setCriteria("d.OBJET_DOSSIER IS NOT NULL");
            $search->sup("DATEDIFF(CURDATE(), d.DATEINSERT_DOSSIER)", (int) $duree_en_jour);
            $search->order("d.DATEINSERT_DOSSIER desc");
            return $search->run(false, null, false)->toArray();
        }

        //Fonction qui récup tous les établissements liés au dossier LAST VERSION
        public function getPreventionnistesDossier($id_dossier)
        {

			//retourne la liste des catégories de prescriptions par ordre
            $select = "
                SELECT usrinfos.*
                FROM dossierpreventionniste, utilisateur usr, utilisateurinformations usrinfos
                WHERE dossierpreventionniste.ID_PREVENTIONNISTE = usr.ID_UTILISATEUR
                AND usr.ID_UTILISATEURINFORMATIONS = usrinfos.ID_UTILISATEURINFORMATIONS
                AND dossierpreventionniste.ID_DOSSIER = '".$id_dossier."'
		GROUP BY usr.ID_UTILISATEUR;
            ";
            //echo $select;
            return $this->getAdapter()->fetchAll($select);
        }
    }
