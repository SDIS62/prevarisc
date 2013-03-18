<?php

class Model_DbTable_DossierAffectation extends Zend_Db_Table_Abstract
{
    protected $_name="dossieraffectation"; // Nom de la base
    protected $_primary = array("ID_DATECOMMISSION_AFFECT","ID_DOSSIER_AFFECT"); // Clé primaire

    public function getDossierNonAffect($idDateCom)
    {
        //retourne l'ensemble des dossier programés à la date de comm passée en param et dont les horaires n'ont pas étés précisés
        $select = "SELECT *
            FROM ".$this->_name.", dossier
            WHERE dossier.ID_DOSSIER = ".$this->_name.".ID_DOSSIER_AFFECT
            AND ".$this->_name.".ID_DATECOMMISSION_AFFECT = '".$idDateCom."'
            AND ".$this->_name.".HEURE_DEB_AFFECT IS NULL
            AND ".$this->_name.".HEURE_FIN_AFFECT IS NULL
            ORDER BY NUM_DOSSIER;
        ";
        //echo $select;
        return $this->getAdapter()->fetchAll($select);
    }

    public function getDossierAffect($idDateCom)
    {
        //retourne l'ensemble des dossier programés à la date de comm passée en param et dont les horaires n'ont pas étés précisés
        $select = "SELECT *
            FROM ".$this->_name.", dossier
            WHERE dossier.ID_DOSSIER = ".$this->_name.".ID_DOSSIER_AFFECT
            AND ".$this->_name.".ID_DATECOMMISSION_AFFECT = '".$idDateCom."'
            AND ".$this->_name.".HEURE_DEB_AFFECT IS NOT NULL
            AND ".$this->_name.".HEURE_FIN_AFFECT IS NOT NULL;
        ";
        //echo $select;
        return $this->getAdapter()->fetchAll($select);
    }

    public function getAllDossierAffect($idDateCom)
    {
        //retourne l'ensemble des dossier programés à la date de comm passée en param et dont les horaires n'ont pas étés précisés
        $select = "SELECT ".$this->_name.".*
            FROM ".$this->_name.", dossier
            WHERE dossier.ID_DOSSIER = ".$this->_name.".ID_DOSSIER_AFFECT
            AND ".$this->_name.".ID_DATECOMMISSION_AFFECT = '".$idDateCom."';
        ";
        //echo $select;
        return $this->getAdapter()->fetchAll($select);
    }

    public function recupDateDossierAffect($idDossier)
    {
        //retourne l'ensemble des dossier programés à la date de comm passée en param et dont les horaires n'ont pas étés précisés
        $select = "SELECT *
            FROM ".$this->_name.", datecommission
            WHERE  datecommission.ID_DATECOMMISSION = ".$this->_name.".ID_DATECOMMISSION_AFFECT
            AND ".$this->_name.".ID_DOSSIER_AFFECT = '".$idDossier."'
            ORDER BY DATE_COMMISSION
        ";
        //echo $select;
        return $this->getAdapter()->fetchAll($select);
    }

    public function deleteDateDossierAffect($idDossier)
    {
        //retourne l'ensemble des dossier programés à la date de comm passée en param et dont les horaires n'ont pas étés précisés
        $delete = "DELETE
            FROM ".$this->_name."
            WHERE  ID_DOSSIER_AFFECT = '".$idDossier."'
        ";

        return $this->delete("ID_DOSSIER_AFFECT = '".$idDossier."'");
    }

}
