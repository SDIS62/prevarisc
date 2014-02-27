<?php

class Model_DbTable_DossierAffectation extends Zend_Db_Table_Abstract
{
    protected $_name="dossieraffectation"; // Nom de la base
    protected $_primary = array("ID_DATECOMMISSION_AFFECT","ID_DOSSIER_AFFECT"); // Clé primaire

    public function getDossierNonAffect($idDateCom)
    {
        //retourne l'ensemble des dossiers programés à la date de comm passée en param et dont les horaires N'ONT PAS été précisés
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array("da" => "dossieraffectation"))
			->join(array("d" => "dossier") , "da.ID_DOSSIER_AFFECT = d.ID_DOSSIER")
			->join(array("dn" => "dossiernature"), "dn.ID_DOSSIER = d.ID_DOSSIER")
			->join(array("dnl" => "dossiernatureliste"), "dnl.ID_DOSSIERNATURE = dn.ID_NATURE")
			->join(array("ed"=> "etablissementdossier"), "ed.ID_DOSSIER = d.ID_DOSSIER")
			->join(array("ei"=> "etablissementinformations"), "ed.ID_ETABLISSEMENT = ei.ID_ETABLISSEMENT AND ei.DATE_ETABLISSEMENTINFORMATIONS = (select MAX(DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE ID_ETABLISSEMENT = ed.ID_ETABLISSEMENT)")
			->join(array("ea"=> "etablissementadresse"), "ea.ID_ETABLISSEMENT = ed.ID_ETABLISSEMENT")
			->join(array("ac"=> "adressecommune"), "ac.NUMINSEE_COMMUNE = ea.NUMINSEE_COMMUNE")
			->joinLeft(array('docurba' => 'dossierdocurba'),'docurba.ID_DOSSIER = d.ID_DOSSIER')
			->where("da.ID_DATECOMMISSION_AFFECT = ?",$idDateCom)
			->where("da.HEURE_DEB_AFFECT IS NULL")
			->where("da.HEURE_FIN_AFFECT IS NULL")
			->group("d.ID_DOSSIER")
			->order("da.NUM_DOSSIER");
        //echo $select;
        return $this->getAdapter()->fetchAll($select);
    }

    public function getDossierAffect($idDateCom)
    {
        //retourne l'ensemble des dossiers programés à la date de comm passée en param et dont les horaires ONT été précisés
		
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array("da" => "dossieraffectation"))
			->join(array("d" => "dossier") , "da.ID_DOSSIER_AFFECT = d.ID_DOSSIER")
			->join(array("dn" => "dossiernature"), "dn.ID_DOSSIER = d.ID_DOSSIER")
			->join(array("dnl" => "dossiernatureliste"), "dnl.ID_DOSSIERNATURE = dn.ID_NATURE")
			->join(array("ed"=> "etablissementdossier"), "ed.ID_DOSSIER = d.ID_DOSSIER")
			->join(array("ei"=> "etablissementinformations"), "ed.ID_ETABLISSEMENT = ei.ID_ETABLISSEMENT AND ei.DATE_ETABLISSEMENTINFORMATIONS = (select MAX(DATE_ETABLISSEMENTINFORMATIONS) FROM etablissementinformations WHERE ID_ETABLISSEMENT = ed.ID_ETABLISSEMENT)")
			->join(array("ea"=> "etablissementadresse"), "ea.ID_ETABLISSEMENT = ed.ID_ETABLISSEMENT")
			->join(array("ac"=> "adressecommune"), "ac.NUMINSEE_COMMUNE = ea.NUMINSEE_COMMUNE")
			->joinLeft(array('docurba' => 'dossierdocurba'),'docurba.ID_DOSSIER = d.ID_DOSSIER')
			->where("da.ID_DATECOMMISSION_AFFECT = ?",$idDateCom)
			->where("da.HEURE_DEB_AFFECT IS NOT NULL")
			->where("da.HEURE_FIN_AFFECT IS NOT NULL")
			->group("d.ID_DOSSIER");
        return $this->getAdapter()->fetchAll($select);
    }

    public function getAllDossierAffect($idDateCom)
    {
        $select = "SELECT ".$this->_name.".*
            FROM ".$this->_name.", dossier
            WHERE dossier.ID_DOSSIER = ".$this->_name.".ID_DOSSIER_AFFECT
            AND ".$this->_name.".ID_DATECOMMISSION_AFFECT = '".$idDateCom."';
        ";

        return $this->getAdapter()->fetchAll($select);
    }

    public function recupDateDossierAffect($idDossier)
    {
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
        $delete = "DELETE
            FROM ".$this->_name."
            WHERE  ID_DOSSIER_AFFECT = '".$idDossier."'
        ";

        return $this->delete("ID_DOSSIER_AFFECT = '".$idDossier."'");
    }

}
