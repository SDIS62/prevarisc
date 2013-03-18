<?php

    class Model_DbTable_Statistiques extends Zend_Db_Table_Abstract
    {
        protected $_name="etablissement";
        protected $_primary = "ID_ETABLISSEMENT";

        public $etablissements = null;

        private $ets_date;

        // Début : liste des ERP
        public function listeDesERP($date)
        {
            if($date == null) $date = date("d/m/Y", time());

            $this->ets_date = $date;

            $this->etablissements = $this->select()
                ->setIntegrityCheck(false)
                ->from(array("e" => "etablissement"), array("ID_ETABLISSEMENT"))
                ->columns(array(
                    "DATEVISITE_DOSSIER" => "( SELECT MAX( dossier.DATEVISITE_DOSSIER ) FROM etablissementdossier, dossier, dossiernature, etablissement
                    WHERE dossier.ID_DOSSIER = etablissementdossier.ID_DOSSIER
                    AND dossiernature.ID_DOSSIER = dossier.ID_DOSSIER
                    AND etablissementdossier.ID_ETABLISSEMENT = etablissement.ID_ETABLISSEMENT
                    AND etablissement.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT
                    AND dossiernature.ID_NATURE = '21'
                    AND ( dossier.TYPE_DOSSIER = '2' || dossier.TYPE_DOSSIER = '3')
                    GROUP BY etablissement.ID_ETABLISSEMENT)",
                    "ARRONDISSEMENT" => "(SELECT `groupement`.LIBELLE_GROUPEMENT FROM `groupement` INNER JOIN `groupementcommune` ON groupementcommune.ID_GROUPEMENT = groupement.ID_GROUPEMENT INNER JOIN `groupementtype` ON groupementtype.ID_GROUPEMENTTYPE = groupement.ID_GROUPEMENTTYPE WHERE (groupementcommune.NUMINSEE_COMMUNE = adressecommune.NUMINSEE_COMMUNE AND groupementtype.ID_GROUPEMENTTYPE = 2) LIMIT 1)"
                ))
                ->join("etablissementinformations", "e.ID_ETABLISSEMENT = etablissementinformations.ID_ETABLISSEMENT", array(
                    "LIBELLE_ETABLISSEMENTINFORMATIONS",
                    "ID_CATEGORIE",
                    "ID_TYPE",
                    "ID_COMMISSION",
                    "ID_STATUT",
                    "DATE_ETABLISSEMENTINFORMATIONS"
                ))
                ->joinLeft("avis", "etablissementinformations.ID_AVIS = avis.ID_AVIS", "LIBELLE_AVIS")
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

                $this->etablissements->where("etablissementinformations.PERIODICITE__ETABLISSEMENTINFORMATIONS > 0 AND etablissementinformations.PERIODICITE__ETABLISSEMENTINFORMATIONS IS NOT NULL");

                return $this;
            }
        }

        public function sousAvisDefavorable()
        {
            if ($this->etablissements != null) {

                $this->etablissements->where("avis.ID_AVIS = 3 AND SCHEMAMISESECURITE_ETABLISSEMENTINFORMATIONS != 1");

                $this->etablissements->columns(array(
                    "NBJOURS_DEFAVORABLE" => "(
                    SELECT DATEDIFF('" . $this->getDate($this->ets_date) . "', MAX(DATE_ETABLISSEMENTINFORMATIONS))
                    FROM etablissementinformations
                    WHERE
                        etablissementinformations.ID_ETABLISSEMENT = e.ID_ETABLISSEMENT AND
                        UNIX_TIMESTAMP(etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS) <= UNIX_TIMESTAMP('" . $this->getDate($this->ets_date) . "') OR
                        etablissementinformations.DATE_ETABLISSEMENTINFORMATIONS IS NULL
                    GROUP BY ID_ETABLISSEMENT
                    )
                    "

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
