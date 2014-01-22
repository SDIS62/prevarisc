<?php

class Model_DbTable_DossierNature extends Zend_Db_Table_Abstract
{
    protected $_name="dossiernature"; // Nom de la base
    protected $_primary = "ID_DOSSIERNATURE"; // Clé primaire

    /*

    //prend en parametre un type et retourne toutes les natures associées à ce dossier
    public function getDossierNaturesLibelle($idDossierType)
    {
        $select = "SELECT *
            FROM dossiernature, dossiernatureliste
            WHERE dossiernature.ID_NATURE = dossiernatureliste.ID_DOSSIERNATURE
            AND dossiernature.ID_DOSSIER = '".$idDossierType."'
        ;";
        //echo $select;
        return $this->getAdapter()->fetchAll($select);
    }

    */
    public function getDossierNaturesLibelle($idDossierType)
    {
        $select = "SELECT dossiernatureliste.LIBELLE_DOSSIERNATURE as LIBELLE_DOSSIERNATURE, dossiernature.ID_DOSSIERNATURE as ID_DOSSIERNATURE, dossiernature.ID_NATURE as ID_NATURE
            FROM dossiernature, dossiernatureliste
            WHERE dossiernature.ID_NATURE = dossiernatureliste.ID_DOSSIERNATURE
            AND dossiernature.ID_DOSSIER = '".$idDossierType."'
        ;";
        //echo $select;
        return $this->getAdapter()->fetchAll($select);
    }

    public function getDossierNaturesId($idDossier)
    {
        $select = "SELECT ID_DOSSIERNATURE
            FROM dossiernature
            WHERE ID_DOSSIER = '".$idDossier."'
        ;";
        //echo $select;
        return $this->getAdapter()->fetchRow($select);
    }

	public function getDossierNatureLibelle($idDossier)
    {
        $select = "SELECT dossiernatureliste.LIBELLE_DOSSIERNATURE
            FROM dossiernature, dossiernatureliste
			WHERE dossiernature.ID_NATURE = dossiernatureliste.ID_DOSSIERNATURE
            AND dossiernature.ID_DOSSIER = '".$idDossier."'
        ;";
        //echo $select;
        return $this->getAdapter()->fetchRow($select);
    }

}
