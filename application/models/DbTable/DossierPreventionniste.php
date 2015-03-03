<?php

class Model_DbTable_DossierPreventionniste extends Zend_Db_Table_Abstract
{
    protected $_name="dossierpreventionniste"; // Nom de la base
    protected $_primary = array("ID_DOSSIER","ID_PREVENTIONNISTE"); // Cl� primaire

    public function getPrevDossier($idDossier)
    {
        $select = "SELECT *, ID_UTILISATEUR as uid
            FROM dossierpreventionniste, utilisateur, utilisateurinformations, utilisateurcivilite
            WHERE dossierpreventionniste.ID_DOSSIER = '".$idDossier."'
            AND dossierpreventionniste.ID_PREVENTIONNISTE = utilisateur.ID_UTILISATEUR
            AND utilisateur.ID_UTILISATEURINFORMATIONS = utilisateurinformations.ID_UTILISATEURINFORMATIONS
            AND utilisateurinformations.ID_UTILISATEURCIVILITE = utilisateurcivilite.ID_UTILISATEURCIVILITE
        ;";
        //echo $select;
        return $this->getAdapter()->fetchAll($select);
    }
	
    public function delPrevsDossier($idDossier)
    {
        $select = "DELETE FROM dossierpreventionniste WHERE ID_DOSSIER = '".$idDossier."';";
        //echo $select;
        return $this->getAdapter()->exec($select);
    }

}
