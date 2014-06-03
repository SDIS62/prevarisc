<?php

    class Model_DbTable_Dossier extends Zend_Db_Table_Abstract
    {
		
        protected $_name="dossier"; // Nom de la base
        protected $_primary = "ID_DOSSIER"; // Cl� primaire

        //Fonction qui r�cup�re toutes les infos g�n�rales d'un dossier
        public function getGeneral($id)
        {
            $select = "SELECT *
            FROM dossier, dossiertype, dossiernature, commission, commissiontype
            WHERE dossier.commission_dossier =	commission.id_commission
            AND commission.id_commissiontype = commissiontype.id_commissiontype
            AND dossier.type_dossier = dossiertype.id_dossiertype
            AND dossier.nature_dossier = dossiernature.id_dossiernature
            AND dossier.id_dossier = '".$id."';";
            //echo $select;
            return $this->getAdapter()->fetchRow($select);
        }

        //Fonction qui r�cup�re tous les �tablissements concern�s par le dossier
        //PAS CERTAIN QU4ELLE SOIT ENCORE UTILIS2E
        public function getEtablissementLibelleListe($id_etablissement)
        {
            $select = "SELECT etablissementlibelle.*
            FROM etablissementlibelle
            WHERE etablissementlibelle.id_etablissement = '".$id_etablissement."'
            AND etablissementlibelle.date_etablissementlibelle = (
                SELECT MAX(etablissementlibelle.date_etablissementlibelle)
                FROM etablissementlibelle
                WHERE etablissementlibelle.id_etablissement = '".$id_etablissement."'
            );";

            //return $select;
            return $this->getAdapter()->fetchAll($select);
        }

        //Fonction qui r�cup�re tous les �tablissements li�s au dossier LAST VERSION
        public function getEtablissementDossier($id_dossier)
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

        //autocompletion utilis� dans la partie dossier - Recherche etablissement LAST VERSION
        public function searchLibelleEtab( $etablissementLibelle )
        {
            $select = "
                SELECT ID_ETABLISSEMENT, LIBELLE_ETABLISSEMENTINFORMATIONS, LIBELLE_GENRE
                FROM etablissementinformations t1,genre
                WHERE genre.ID_GENRE = t1.ID_GENRE
                AND LIBELLE_ETABLISSEMENTINFORMATIONS LIKE '%".$etablissementLibelle."%'
                AND t1.DATE_ETABLISSEMENTINFORMATIONS = (
                  SELECT MAX(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS)
                  FROM etablissementinformations
                  WHERE t1.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT
                )
            ";

            //return $select;
            return $this->getAdapter()->fetchAll($select);
        }

        //Fonction qui r�cup�re toutes les c�llules concern�es par le dossier
        public function getCelluleListe($id_dossier)
        {
            $select = "SELECT cellulelibelle.*, MAX(cellulelibelle.date_cellulelibelle)
            FROM celluledossier, cellulelibelle
            WHERE cellulelibelle.id_cellule = celluledossier.id_cellule
            AND celluledossier.id_dossier = '".$id_dossier."'
            GROUP BY cellulelibelle.id_cellule;";

            //return $select ;
            return $this->getAdapter()->fetchAll($select);
        }

        //retourne 1 si dossier Etude - 0 si Visite
        public function getTypeDossier($id_dossier)
        {
            $select = "SELECT dossier.TYPE_DOSSIER
            FROM dossier
            WHERE dossier.id_dossier = '".$id_dossier."';";

            //echo $select;
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

        public function getCommissionDossier($id_dossier)
        {
            $select = "SELECT commission_dossier
            FROM dossier
            WHERE id_dossier = '".$id_dossier."';";

            //return $select;
            return $this->getAdapter()->fetchRow($select);
        }

        public function getGenerationInfos($id_dossier)
        {
            $select = "
                SELECT dossier.*, dossiertype.*, commission.*, commissiontype.*
                FROM dossier, dossiertype, commission, commissiontype
                WHERE dossier.commission_dossier =	commission.id_commission
                AND commission.id_commissiontype = commissiontype.id_commissiontype
                AND dossier.TYPE_DOSSIER = dossiertype.id_dossiertype
                AND dossier.id_dossier = '".$id_dossier."';
            ";

            return $this->getAdapter()->fetchRow($select);
            //return $select;
        }

        // Retourne la liste de tout les dossiers (�tudes et/ou visite) d'un �tablissement
        // Si type vaut 1 : visites ; 0 : �tudes
        public function getDossiersEtablissement($etablissement, $type = null)
        {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from("etablissementdossier", null)
                ->join("dossier", "etablissementdossier.ID_DOSSIER = dossier.ID_DOSSIER", array("ID_DOSSIER","LIBELLE_DOSSIER", "OBJET_DOSSIER", "DESCRIPTIFGEN_DOSSIER", "DATESECRETARIAT_DOSSIER"))
                ->join("dossiertype", "dossier.TYPE_DOSSIER = dossiertype.ID_DOSSIERTYPE", "VISITEBOOL_DOSSIERTYPE")
                ->where("etablissementdossier.ID_ETABLISSEMENT = $etablissement")
                ->order("dossier.DATESECRETARIAT_DOSSIER DESC");

            if($type == "1" || $type == "0")
                $select->where("dossiertype.VISITEBOOL_DOSSIERTYPE = $type");

            return $this->fetchAll($select)->toArray();
        }
		
        public function getLastInfosEtab( $idEtablissement)
        {
            $select = "SELECT ID_ETABLISSEMENT, LIBELLE_ETABLISSEMENTINFORMATIONS, LIBELLE_GENRE
            FROM etablissementinformations,genre
            WHERE genre.ID_GENRE = etablissementinformations.ID_GENRE
            AND LIBELLE_ETABLISSEMENTINFORMATIONS LIKE '%".$etablissementLibelle."%';";

            //return $select;
            return $this->getAdapter()->fetchAll($select);
        }

        public function getDossierEtab($idEtablissement,$idDossier)
        {
            $select = "SELECT *
            FROM dossier, etablissementdossier, dossiertype
            WHERE dossier.ID_DOSSIER = etablissementdossier.ID_DOSSIER
            AND etablissementdossier.ID_ETABLISSEMENT = '".$idEtablissement."'
            AND dossiertype.ID_DOSSIERTYPE = dossier.TYPE_DOSSIER

            AND dossier.ID_DOSSIER NOT IN (
                SELECT ID_DOSSIER1
                FROM dossierlie
                WHERE ID_DOSSIER2 = ".$idDossier."
            )
            AND dossier.ID_DOSSIER NOT IN (
                SELECT ID_DOSSIER2
                FROM dossierlie
                WHERE ID_DOSSIER1 = ".$idDossier."
            )
            ORDER BY dossier.DATEINSERT_DOSSIER
            ;";
            //echo $select;
            return $this->getAdapter()->fetchAll($select);
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
				->from(array('d' => 'dossier'),"max(d.DATEVISITE_DOSSIER) as maxdate")
				->join(array('ed' => 'etablissementdossier') , "ed.ID_DOSSIER = d.ID_DOSSIER")
				->join(array("dn" => "dossiernature") , "d.ID_DOSSIER = dn.ID_DOSSIER")
				->where("ed.ID_ETABLISSEMENT = ?",$idEtab)
				->where("dn.ID_NATURE = 21 OR dn.ID_NATURE = 26")
				->where("d.DATEVISITE_DOSSIER IS NOT NULL");
				
			//echo $select->__toString();
			return $this->getAdapter()->fetchRow($select);	

		}

		public function findLastVpCreationDoc($idEtab,$idDossier,$dateVisite)
		{
			$select = $this->select()
				->setIntegrityCheck(false)
				->from(array('d' => 'dossier'),"max(d.DATEVISITE_DOSSIER) as maxdate")
				->join(array('ed' => 'etablissementdossier') , "ed.ID_DOSSIER = d.ID_DOSSIER")
				->join(array("dn" => "dossiernature") , "d.ID_DOSSIER = dn.ID_DOSSIER")
				->where("ed.ID_ETABLISSEMENT = ?",$idEtab)
				->where("ed.ID_DOSSIER <> ?",$idDossier)
				->where("dn.ID_NATURE = 21 OR dn.ID_NATURE = 26")
				->where("d.DATEVISITE_DOSSIER IS NOT NULL")
				->where("d.DATEVISITE_DOSSIER < ?",$dateVisite);
				
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
				AND (genre.ID_GENRE = 2 || genre.ID_GENRE = 3)
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
    }
