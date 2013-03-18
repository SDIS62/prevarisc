<?php

class Model_DbTable_PrescriptionDossier extends Zend_Db_Table_Abstract
{

    protected $_name="prescriptiondossier"; // Nom de la base
    protected $_primary = "ID_PRESCRIPTIONDOSSIER"; // Clé primaire

    //Fonction qui récupère toutes les infos générales d'un dossier



    public function getListePrescription($id_dossier)
    {
        $select = "SELECT *
            FROM prescriptiondossier
            WHERE IDDOSSIER_PRESCRIPTIONDOSSIER = '".$id_dossier."'
            ORDER BY NUM_PRESCRIPTIONDOSSIER;
        ";
        //return $select;
        return $this->getAdapter()->fetchAll($select);
    }

    public function getPrescriptionAssoc($idPrescription)
    {
        $select = "SELECT *
            FROM prescriptionassoc, prescriptionlibelle
            WHERE prescriptionassoc.LIBELLE_PRESCRIPTIONASSOC = prescriptionlibelle.ID_PRESCRIPTIONLIBELLE
            AND ID_PRESCRIPTIONASSOC = '".$idPrescription."';
        ";
        //return $select;
        return $this->getAdapter()->fetchRow($select);
    }

    public function getPrescriptionType($idPrescription)
    {
        $select = "SELECT *
            FROM prescriptiontype, prescriptionlibelle
            WHERE prescriptiontype.LIBELLE_PRESCRIPTIONTYPE = prescriptionlibelle.ID_PRESCRIPTIONLIBELLE
            AND ID_PRESCRIPTIONTYPE = '".$idPrescription."';
        ";
        //return $select;
        return $this->getAdapter()->fetchRow($select);
    }

}
