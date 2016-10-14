<?php

    class Model_DbTable_Statistiques extends Zend_Db_Table_Abstract
    {
        protected $_name="etablissement";
        protected $_primary = "ID_ETABLISSEMENT";

        public $etablissements = null;

        private $ets_date;
        private $ets_dateDebut;
        private $ets_dateFin;

        // D�but : liste des ERP
        public function listeDesERP($date)
        {
            if($date == null) $date = date("d/m/Y", time());

            $this->ets_date = $date;

            $this->etablissements = $this->select()
                ->setIntegrityCheck(false)
                ->from(array("e" => "etablissement"), array("ID_ETABLISSEMENT"))
                ->columns(array(
                    "DATEVISITE_DOSSIER" => new Zend_Db_Expr("( SELECT MAX( dossier.DATEVISITE_DOSSIER ) FROM etablissementdossier, dossier, dossiernature, etablissement
                    WHERE dossier.ID_DOSSIER = etablissementdossier.ID_DOSSIER
                    AND dossiernature.ID_DOSSIER = dossier.ID_DOSSIER
                    AND etablissementdossier.ID_ETABLISSEMENT = etablissement.ID_ETABLISSEMENT
                    AND etablissement.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT
                    AND dossiernature.ID_NATURE = '21'
                    AND ( dossier.TYPE_DOSSIER = '2' || dossier.TYPE_DOSSIER = '3')
                    GROUP BY etablissement.ID_ETABLISSEMENT)"),
                    "ARRONDISSEMENT" =>  new Zend_Db_Expr("(SELECT `groupement`.LIBELLE_GROUPEMENT FROM `groupement` INNER JOIN `groupementcommune` ON groupementcommune.ID_GROUPEMENT = groupement.ID_GROUPEMENT INNER JOIN `groupementtype` ON groupementtype.ID_GROUPEMENTTYPE = groupement.ID_GROUPEMENTTYPE WHERE (groupementcommune.NUMINSEE_COMMUNE = adressecommune.NUMINSEE_COMMUNE AND groupementtype.ID_GROUPEMENTTYPE = 2) LIMIT 1)")
                ))
                ->join("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT", array(
                    "LIBELLE_ETABLISSEMENTINFORMATIONS",
                    "ID_CATEGORIE",
                    "ID_TYPE",
                    "ID_COMMISSION",
                    "ID_STATUT",
                    "DATE_ETABLISSEMENTINFORMATIONS"
                ))
                ->joinLeft("type", "etablissementinformations.ID_TYPE= type.ID_TYPE ","LIBELLE_TYPE")
                ->joinLeft("dossier", "e.ID_DOSSIER_DONNANT_AVIS = dossier.ID_DOSSIER", array("ID_AVIS" => "AVIS_DOSSIER_COMMISSION"))
                ->joinLeft("avis", "dossier.AVIS_DOSSIER = avis.ID_AVIS", array("LIBELLE_AVIS" => "LIBELLE_AVIS"))
                ->joinLeft("commission", "commission.ID_COMMISSION = etablissementinformations.ID_COMMISSION", "LIBELLE_COMMISSION")
                ->joinLeft("categorie", "etablissementinformations.ID_CATEGORIE = categorie.ID_CATEGORIE", "LIBELLE_CATEGORIE")
                ->joinLeft("etablissementadresse", "e.ID_ETABLISSEMENT = etablissementadresse.ID_ETABLISSEMENT", array("NUMERO_ADRESSE", "COMPLEMENT_ADRESSE"))
                ->joinLeft("adressecommune", "etablissementadresse.NUMINSEE_COMMUNE = adressecommune.NUMINSEE_COMMUNE", array("LIBELLE_COMMUNE", "CODEPOSTAL_COMMUNE"))
                ->joinLeft("adresserue", "etablissementadresse.NUMINSEE_COMMUNE = adresserue.NUMINSEE_COMMUNE AND etablissementadresse.ID_RUE = adresserue.ID_RUE")
                ->where("ID_GENRE = 2") // Pas de site - IGH
                ->where("etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = (
                    SELECT MAX(DATE_ETABLISSEMENTINFORMATIONS)
                    FROM etablissementinformations
                    WHERE
                        etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT AND
                        UNIX_TIMESTAMP(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) <= UNIX_TIMESTAMP('" . $this->getDate($date) . "') OR
                        etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS IS NULL
                    )
                ")
                ->group("ID_ETABLISSEMENT");

            return $this;
        }
        
        public function listeDesERPVisitePeriodique($dateDebut, $dateFin)
        {
            if($dateDebut == null) $dateDebut = date("01/01/".date("Y"), time());
            if($dateFin == null) $dateFin = date("31/12/".date("Y"), time());
            
            $this->ets_dateDebut = $dateDebut;
            $this->ets_dateFin = $dateFin;

            $this->etablissements = $this->select()
                ->setIntegrityCheck(false)
                ->from(array("e" => "etablissement"), array("ID_ETABLISSEMENT"))
                ->columns(array(
                    "DATEVISITE_DOSSIER" => new Zend_Db_Expr("( SELECT MAX( dossier.DATEVISITE_DOSSIER ) FROM etablissementdossier, dossier, dossiernature, etablissement
                    WHERE dossier.ID_DOSSIER = etablissementdossier.ID_DOSSIER
                    AND DATEDIFF(dossier.DATEVISITE_DOSSIER,CURDATE()) > 0
                    AND dossiernature.ID_DOSSIER = dossier.ID_DOSSIER
                    AND etablissementdossier.ID_ETABLISSEMENT = etablissement.ID_ETABLISSEMENT
                    AND etablissement.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT
                    AND dossiernature.ID_NATURE in (21,26) 
                    AND dossier.TYPE_DOSSIER in (2,3)
                    GROUP BY etablissement.ID_ETABLISSEMENT)"),
                    "ARRONDISSEMENT" => new Zend_Db_Expr("(SELECT `groupement`.LIBELLE_GROUPEMENT FROM `groupement` INNER JOIN `groupementcommune` ON groupementcommune.ID_GROUPEMENT = groupement.ID_GROUPEMENT INNER JOIN `groupementtype` ON groupementtype.ID_GROUPEMENTTYPE = groupement.ID_GROUPEMENTTYPE WHERE (groupementcommune.NUMINSEE_COMMUNE = adressecommune.NUMINSEE_COMMUNE AND groupementtype.ID_GROUPEMENTTYPE = 2) LIMIT 1)")
                ))
                ->join("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT", array(
                    "LIBELLE_ETABLISSEMENTINFORMATIONS",
                    "ID_CATEGORIE",
                    "ID_TYPE",
                    "ID_COMMISSION",
                    "ID_STATUT",
                    "DATE_ETABLISSEMENTINFORMATIONS",
                    "PERIODICITE_ETABLISSEMENTINFORMATIONS",
                    "e.ID_ETABLISSEMENT"
                ))
                ->joinLeft("type", "etablissementinformations.ID_TYPE= type.ID_TYPE ","LIBELLE_TYPE")
                ->joinLeft("etablissementinformationspreventionniste", "etablissementinformations.ID_ETABLISSEMENTINFORMATIONS  = etablissementinformationspreventionniste.ID_ETABLISSEMENTINFORMATIONS")
                ->joinLeft("utilisateur", "utilisateur.ID_UTILISATEUR = etablissementinformationspreventionniste.ID_UTILISATEUR")
                ->joinLeft("utilisateurinformations", "utilisateur.ID_UTILISATEURINFORMATIONS = utilisateurinformations.ID_UTILISATEURINFORMATIONS",array("NOM_UTILISATEURINFORMATIONS","PRENOM_UTILISATEURINFORMATIONS"))    
                ->joinLeft("dossier", "e.ID_DOSSIER_DONNANT_AVIS = dossier.ID_DOSSIER", array("ID_AVIS" => "AVIS_DOSSIER_COMMISSION"))
                ->joinLeft("avis", "dossier.AVIS_DOSSIER = avis.ID_AVIS", array("LIBELLE_AVIS" => "LIBELLE_AVIS"))
                ->joinLeft("commission", "commission.ID_COMMISSION = etablissementinformations.ID_COMMISSION", "LIBELLE_COMMISSION")
                ->joinLeft("categorie", "etablissementinformations.ID_CATEGORIE = categorie.ID_CATEGORIE", "LIBELLE_CATEGORIE")
                ->joinLeft("etablissementadresse", "e.ID_ETABLISSEMENT = etablissementadresse.ID_ETABLISSEMENT", array("NUMERO_ADRESSE", "COMPLEMENT_ADRESSE"))
                ->joinLeft("adressecommune", "etablissementadresse.NUMINSEE_COMMUNE = adressecommune.NUMINSEE_COMMUNE", array("LIBELLE_COMMUNE", "CODEPOSTAL_COMMUNE"))
                ->joinLeft("adresserue", "etablissementadresse.NUMINSEE_COMMUNE = adresserue.NUMINSEE_COMMUNE AND etablissementadresse.ID_RUE = adresserue.ID_RUE")
                ->where("ID_GENRE = 2") // Pas de site - IGH
                ->where("etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS = (
                    SELECT MAX(DATE_ETABLISSEMENTINFORMATIONS)
                    FROM etablissementinformations
                    WHERE
                        etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT AND
                        UNIX_TIMESTAMP(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) >= UNIX_TIMESTAMP('" . $this->getDate($dateDebut) . "')
                        AND UNIX_TIMESTAMP(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) <= UNIX_TIMESTAMP('" . $this->getDate($dateFin) . "')
                        OR  etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS IS NULL
                    )
                ")
                ->group("ID_ETABLISSEMENT");

            return $this;
        }
         
        

        // CHAMPS SUPPLEMENTAIRES
        public function enExploitation()
        {
            if ($this->etablissements != null) {

                // if($date == null) $date = date("d/m/Y", time());

                $this->etablissements->where("ID_STATUT = 2");
                // $this->etablissements->where("UNIX_TIMESTAMP(e.DATEENREGISTREMENT_ETABLISSEMENT) <= UNIX_TIMESTAMP(?)", $this->getDate($date));
                return $this;
            }
        }

        public function sousmisAControle()
        {
            if ($this->etablissements != null) {

                /*
                $this->etablissements->joinLeft(array("pERP" => "periodicite"), "pERP.ID_CATEGORIE = etablissementinformations.ID_CATEGORIE AND pERP.ID_TYPE = etablissementinformations.ID_TYPE AND pERP.LOCALSOMMEIL_PERIODICITE = etablissementinformations.LOCALSOMMEIL_ETABLISSEMENTINFORMATIONS");
                $this->etablissements->joinLeft(array("pIGH" => "periodicite"), "pIGH.ID_TYPE = etablissementinformations.ID_CLASSE AND pIGH.ID_CATEGORIE = 0");

                $this->etablissements->where("(pERP.ID_CATEGORIE != 0 AND pERP.PERIODICITE_PERIODICITE != 0) OR (pIGH.ID_CATEGORIE = 0 AND pIGH.PERIODICITE_PERIODICITE != 0)");
                */

                $this->etablissements->where("etablissementinformations.PERIODICITE_ETABLISSEMENTINFORMATIONS > 0 AND etablissementinformations.PERIODICITE_ETABLISSEMENTINFORMATIONS IS NOT NULL");

                return $this;
            }
        }

        public function sousAvisDefavorable()
        {
            if ($this->etablissements != null) {

                $this->etablissements->where("dossier.AVIS_DOSSIER_COMMISSION = 2"); // AND SCHEMAMISESECURITE_ETABLISSEMENTINFORMATIONS != 1

                $this->etablissements->columns(array(
                    "NBJOURS_DEFAVORABLE" => new Zend_Db_Expr("(
                    SELECT DATEDIFF('" . $this->getDate($this->ets_date) . "', MAX(DATE_ETABLISSEMENTINFORMATIONS))
                    FROM etablissementinformations
                    WHERE
                        etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT AND
                        UNIX_TIMESTAMP(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) <= UNIX_TIMESTAMP('" . $this->getDate($this->ets_date) . "') OR
                        etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS IS NULL
                    GROUP BY ID_ETABLISSEMENT
                    )
                    ")

                ));

                return $this;
            }
        }

        public function surLaCommune($commune)
        {
            if ($this->etablissements != null) {

                $this->etablissements->where("adressecommune.NUMINSEE_COMMUNE = ?", $commune);

                return $this;
            }
        }

        // Fonctions
        public function trierPar($col)
        {
            $this->etablissements->order($col);

            return $this;
        }

        private function getDate($input)
        {
            $array_date = explode("/", $input);
            if (!is_array($array_date) || count($array_date) != 3) {

                throw new Exception('Erreur dans la date', 500);
            }

            return $array_date[2]."-".$array_date[1]."-".$array_date[0]." 00:00:00";
        }

        public function go()
        {
            if($this->etablissements != null) return $this->fetchAll($this->etablissements)->toArray();
        }

    }
