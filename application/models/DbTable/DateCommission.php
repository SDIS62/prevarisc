<?php
    class Model_DbTable_DateCommission extends Zend_Db_Table_Abstract
    {
        protected $_name="datecommission"; // Nom de la base
        protected $_primary = "ID_DATECOMMISSION"; // Cl� primaire

        public function addDateComm($date,$heureD,$heureF,$idComm,$type,$libelle)
        {
            $new = $this->createRow();
            $new->DATE_COMMISSION= $date;
            $new->HEUREDEB_COMMISSION= $heureD;
            $new->HEUREFIN_COMMISSION= $heureF;
            $new->COMMISSION_CONCERNE = $idComm;
            $new->ID_COMMISSIONTYPEEVENEMENT = $type;
            $new->LIBELLE_DATECOMMISSION = $libelle;
            $new->save();

            return $new->ID_DATECOMMISSION;
        }

        public function addDateCommLiee($date,$heureD,$heureF,$idCommOrigine,$type,$idComm,$libelle)
        {
            $new = $this->createRow();
            $new->DATE_COMMISSION= $date;
            $new->HEUREDEB_COMMISSION= $heureD;
            $new->HEUREFIN_COMMISSION= $heureF;
            $new->DATECOMMISSION_LIEES = $idCommOrigine;
            $new->ID_COMMISSIONTYPEEVENEMENT = $type;
            $new->COMMISSION_CONCERNE = $idComm;
            $new->LIBELLE_DATECOMMISSION = $libelle;
            $new->save();

            return $new->ID_DATECOMMISSION;
        }

        public function getFirstCommission($idCommission,$debut,$fin)
        {
            $select = "SELECT *
                FROM datecommission
                WHERE COMMISSION_CONCERNE = '".$idCommission."'
                AND DATE_COMMISSION BETWEEN '".$debut."'	AND '".$fin."'
            ";
            //echo $select;

            return $this->getAdapter()->fetchAll($select);
        }
        public function getNextCommission($date, $next_date)
        {
            $select = "SELECT *
                FROM datecommission d
                LEFT JOIN commission c ON d.COMMISSION_CONCERNE = c.ID_COMMISSION
                WHERE DATE_COMMISSION BETWEEN '".date('Y-m-d', $date)."' AND '".date('Y-m-d', $next_date)."'
                ORDER BY DATE_COMMISSION, HEUREDEB_COMMISSION";
            return $this->getAdapter()->fetchAll($select);
        }
        
        public function getMonthCommission($mois,$annee,$idcom)
        {
            $select = "SELECT *
                FROM datecommission
                WHERE MONTH(DATE_COMMISSION) = '".$mois."'  AND   YEAR(DATE_COMMISSION) = '".$annee."'
                AND COMMISSION_CONCERNE = '".$idcom."'";
            return $this->getAdapter()->fetchAll($select);
        }

        public function getCommissionsLiees($idCommissionOrigine,$debut,$fin)
        {
            $select = "SELECT *
                FROM datecommission
                WHERE DATECOMMISSION_LIEES = '".$idCommissionOrigine."'
                AND DATE_COMMISSION BETWEEN '".$debut."'	AND '".$fin."'
            ";
            //echo $select;
            return $this->getAdapter()->fetchAll($select);
        }

        public function getCommissionsQtypListing($idComm)
        {
            $select = "SELECT *
                FROM datecommission
                WHERE ( ID_DATECOMMISSION = '".$idComm."'
                OR DATECOMMISSION_LIEES = '".$idComm."' )
                ORDER BY DATE_COMMISSION
            ";
            //echo $select;

            return $this->getAdapter()->fetchAll($select);
        }

        public function dateCommUpdateLibelle($idComm, $libelle)
        {
            $select = "UPDATE datecommission
                SET LIBELLE_DATECOMMISSION = '".$libelle."'
                WHERE ( ID_DATECOMMISSION = '".$idComm."' OR DATECOMMISSION_LIEES = '".$idComm."' )
            ";
            //echo $select;
            return $this->getAdapter()->query($select);
        }

        public function dateCommUpdateType($idComm, $idNewType)
        {
            $select = "UPDATE datecommission
                SET ID_COMMISSIONTYPEEVENEMENT = '".$idNewType."'
                WHERE ( ID_DATECOMMISSION = '".$idComm."' OR DATECOMMISSION_LIEES = '".$idComm."' )
            ";
            //echo $select;
            return $this->getAdapter()->query($select);
        }

        public function changeMasterDateComm($oldComm, $newComm)
        {
            $select = "UPDATE datecommission
                SET DATECOMMISSION_LIEES = '".$newComm."'
                WHERE ( ID_DATECOMMISSION = '".$oldComm."' OR DATECOMMISSION_LIEES = '".$oldComm."' )
            ";
            //echo $select;
            return $this->getAdapter()->query($select);
        }

        //pour la gestion des ordres du jour r�cup des date li�es
        public function getCommissionsDateLieesMaster($idComm)
        {
            $select = "SELECT *
                FROM datecommission
                WHERE ( ID_DATECOMMISSION = '".$idComm."'
                OR DATECOMMISSION_LIEES = '".$idComm."' )
                ORDER BY DATE_COMMISSION
            ";
            //echo $select;
            return $this->getAdapter()->fetchAll($select);
        }

    }
