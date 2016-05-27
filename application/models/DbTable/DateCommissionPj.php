<?php

class Model_DbTable_DateCommissionPj extends Zend_Db_Table_Abstract
{

    protected $_name="datecommissionpj"; // Nom de la base
    protected $_primary = "ID_DATECOMMISSION";// Clé primaire

    //récupération de la liste des dossiers prévu à la date de commission passée en paramètres
/*
    public function getDossiersInfosOLD($dateCommId)
    {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('doss' => 'dossier'))
			->join(array('dossAffect' => 'dossieraffectation'),'doss.ID_DOSSIER = dossAffect.ID_DOSSIER_AFFECT')
			->join(array('dateComm' => 'datecommission'),'dossAffect.ID_DATECOMMISSION_AFFECT = dateComm.ID_DATECOMMISSION')	
			->join(array('etabDoss' => 'etablissementdossier'),'etabDoss.ID_DOSSIER = doss.ID_DOSSIER')
			->join(array('etabInfos' => 'etablissementinformations'),'etabInfos.ID_ETABLISSEMENT = etabDoss.ID_ETABLISSEMENT AND etabInfos.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(DATE_ETABLISSEMENTINFORMATIONS) FROM  etablissementinformations WHERE ID_ETABLISSEMENT = etabDoss.ID_ETABLISSEMENT)')
			->join(array('etabAdr' => 'etablissementadresse'),'etabDoss.ID_ETABLISSEMENT = etabAdr.ID_ETABLISSEMENT')
			->join(array('adrComm' => 'adressecommune'),'etabAdr.NUMINSEE_COMMUNE = adrComm.NUMINSEE_COMMUNE')
			->join(array('dossNat' => 'dossiernature'),'dossNat.ID_DOSSIER = doss.ID_DOSSIER')
			->join(array('dossNatListe' => 'dossiernatureliste'),'dossNat.ID_NATURE = dossNatListe.ID_DOSSIERNATURE')
			->joinLeft(array('docurba' => 'dossierdocurba'),'docurba.ID_DOSSIER = doss.ID_DOSSIER')
			->where('dateComm.ID_DATECOMMISSION = ?',$dateCommId)
			->group('doss.ID_DOSSIER')
			->order('etabAdr.NUMINSEE_COMMUNE');

        return $this->getAdapter()->fetchAll($select);
    }
*/
	public function getDossiersInfos($dateCommId)
    {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('doss' => 'dossier'))
			->join(array('dossAffect' => 'dossieraffectation'),'doss.ID_DOSSIER = dossAffect.ID_DOSSIER_AFFECT')
			->join(array('dateComm' => 'datecommission'),'dossAffect.ID_DATECOMMISSION_AFFECT = dateComm.ID_DATECOMMISSION')	
			->join(array('dossNat' => 'dossiernature'),'dossNat.ID_DOSSIER = doss.ID_DOSSIER')
			->join(array('dossNatListe' => 'dossiernatureliste'),'dossNat.ID_NATURE = dossNatListe.ID_DOSSIERNATURE')
			->where('dateComm.ID_DATECOMMISSION = ?',$dateCommId)
			->group('doss.ID_DOSSIER');

        return $this->getAdapter()->fetchAll($select);
    }
	
	
    public function getDossiersInfosByHour($dateCommId)
    {
		//Retourne les dossiers avec toutes les informations le concernant class�s par heure
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('doss' => 'dossier'))
			->join(array('dossAffect' => 'dossieraffectation'),'doss.ID_DOSSIER = dossAffect.ID_DOSSIER_AFFECT')
			->join(array('dateComm' => 'datecommission'),'dossAffect.ID_DATECOMMISSION_AFFECT = dateComm.ID_DATECOMMISSION')
			/*
			->join(array('etabDoss' => 'etablissementdossier'),'etabDoss.ID_DOSSIER = doss.ID_DOSSIER')
			->join(array('etabInfos' => 'etablissementinformations'),'etabInfos.ID_ETABLISSEMENT = etabDoss.ID_ETABLISSEMENT AND etabInfos.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(DATE_ETABLISSEMENTINFORMATIONS) FROM  etablissementinformations WHERE ID_ETABLISSEMENT = etabDoss.ID_ETABLISSEMENT)')
			->join(array('etabAdr' => 'etablissementadresse'),'etabDoss.ID_ETABLISSEMENT = etabAdr.ID_ETABLISSEMENT')
			->join(array('adrComm' => 'adressecommune'),'etabAdr.NUMINSEE_COMMUNE = adrComm.NUMINSEE_COMMUNE')
			*/
			->join(array('dossNat' => 'dossiernature'),'dossNat.ID_DOSSIER = doss.ID_DOSSIER')
			->join(array('dossNatListe' => 'dossiernatureliste'),'dossNat.ID_NATURE = dossNatListe.ID_DOSSIERNATURE')
			//->joinLeft(array('docurba' => 'dossierdocurba'),'docurba.ID_DOSSIER = doss.ID_DOSSIER')
			->where('dateComm.ID_DATECOMMISSION = ?',$dateCommId)
			->group('doss.ID_DOSSIER')
			->order('dossAffect.HEURE_DEB_AFFECT');

        return $this->getAdapter()->fetchAll($select);
    }

    public function getDossiersInfosByOrder($dateCommId)
    {
		//Retourne les dossiers avec toutes les informations le concernant class�s par ordre
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('doss' => 'dossier'))
			->join(array('dossAffect' => 'dossieraffectation'),'doss.ID_DOSSIER = dossAffect.ID_DOSSIER_AFFECT')
			->join(array('dateComm' => 'datecommission'),'dossAffect.ID_DATECOMMISSION_AFFECT = dateComm.ID_DATECOMMISSION')
			/*
			->join(array('etabDoss' => 'etablissementdossier'),'etabDoss.ID_DOSSIER = doss.ID_DOSSIER')
			->join(array('etabInfos' => 'etablissementinformations'),'etabInfos.ID_ETABLISSEMENT = etabDoss.ID_ETABLISSEMENT AND etabInfos.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(DATE_ETABLISSEMENTINFORMATIONS) FROM  etablissementinformations WHERE ID_ETABLISSEMENT = etabDoss.ID_ETABLISSEMENT)')
			->join(array('etabAdr' => 'etablissementadresse'),'etabDoss.ID_ETABLISSEMENT = etabAdr.ID_ETABLISSEMENT')
			->join(array('adrComm' => 'adressecommune'),'etabAdr.NUMINSEE_COMMUNE = adrComm.NUMINSEE_COMMUNE')
			*/
			->join(array('dossNat' => 'dossiernature'),'dossNat.ID_DOSSIER = doss.ID_DOSSIER')
			->join(array('dossNatListe' => 'dossiernatureliste'),'dossNat.ID_NATURE = dossNatListe.ID_DOSSIERNATURE')
			//->joinLeft(array('docurba' => 'dossierdocurba'),'docurba.ID_DOSSIER = doss.ID_DOSSIER')
			->where('dateComm.ID_DATECOMMISSION = ?',$dateCommId)
			->group('doss.ID_DOSSIER')
			->order('dossAffect.NUM_DOSSIER');
		
		//echo $select->__toString();
		
        return $this->getAdapter()->fetchAll($select);
    }

	public function TESTRECUPDOSS($dateCommId)
    {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('doss' => 'dossier'))
			->join(array('dossAffect' => 'dossieraffectation'),'doss.ID_DOSSIER = dossAffect.ID_DOSSIER_AFFECT')
			->join(array('dateComm' => 'datecommission'),'dossAffect.ID_DATECOMMISSION_AFFECT = dateComm.ID_DATECOMMISSION')
			->join(array('dossNat' => 'dossiernature'),'dossNat.ID_DOSSIER = doss.ID_DOSSIER')
			->join(array('dossNatListe' => 'dossiernatureliste'),'dossNat.ID_NATURE = dossNatListe.ID_DOSSIERNATURE')
			->where('dateComm.ID_DATECOMMISSION = ?',$dateCommId)
			->group('doss.ID_DOSSIER')
			->order('dossAffect.NUM_DOSSIER');

        return $this->getAdapter()->fetchAll($select);
    }
	
	public function TESTRECUPDOSSHEURE($dateCommId)
    {
		$select = $this->select()
			->setIntegrityCheck(false)
			->from(array('doss' => 'dossier'))
			->join(array('dossAffect' => 'dossieraffectation'),'doss.ID_DOSSIER = dossAffect.ID_DOSSIER_AFFECT')
			->join(array('dateComm' => 'datecommission'),'dossAffect.ID_DATECOMMISSION_AFFECT = dateComm.ID_DATECOMMISSION')
			->join(array('dossNat' => 'dossiernature'),'dossNat.ID_DOSSIER = doss.ID_DOSSIER')
			->join(array('dossNatListe' => 'dossiernatureliste'),'dossNat.ID_NATURE = dossNatListe.ID_DOSSIERNATURE')
			->where('dateComm.ID_DATECOMMISSION = ?',$dateCommId)
			->where('dossAffect.HEURE_DEB_AFFECT IS NOT NULL')
			->group('doss.ID_DOSSIER')
			->order('dossAffect.HEURE_DEB_AFFECT');

        return $this->getAdapter()->fetchAll($select);
    }
	
	public function getPjInfos($idComm)
	{
		$select = $this->select()
			->setIntegrityCheck(false)
			->from("piecejointe")
			->join("datecommissionpj", "datecommissionpj.ID_PIECEJOINTE = piecejointe.ID_PIECEJOINTE")
			->where("datecommissionpj.ID_DATECOMMISSION = ".$idComm);

		return ( $this->fetchAll( $select ) != null ) ? $this->fetchAll( $select )->toArray() : null;
	}

}
