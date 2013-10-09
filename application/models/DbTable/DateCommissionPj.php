<?php

class Model_DbTable_DateCommissionPj extends Zend_Db_Table_Abstract
{

    protected $_name="datecommissionpj"; // Nom de la base
    protected $_primary = "ID_DATECOMMISSION";// Clé primaire

    //récupération de la liste des dossiers prévu à la date de commission passée en paramètres

    public function getDossiersInfos($dateCommId)
    {
		/*
			$select = "
				SELECT DISTINCT *
				FROM dossier AS doss, datecommission AS dateComm, dossieraffectation AS dossAffect, etablissementdossier AS etabDoss, etablissementinformations as etabInfos, etablissementadresse AS etabAdr, etablissement AS etab, adressecommune AS adrComm, dossiernature AS dossNat, dossiernatureliste AS dossNatListe, dossierdocurba AS dossDocUrba

				WHERE doss.ID_DOSSIER = dossAffect.ID_DOSSIER_AFFECT
				AND dossAffect.ID_DATECOMMISSION_AFFECT = dateComm.ID_DATECOMMISSION
				AND etabDoss.ID_DOSSIER = doss.ID_DOSSIER
				AND etabInfos.ID_ETABLISSEMENT = etabDoss.ID_ETABLISSEMENT
				AND etabDoss.ID_ETABLISSEMENT = etabAdr.ID_ETABLISSEMENT
				AND etabAdr.NUMINSEE_COMMUNE = adrComm.NUMINSEE_COMMUNE
				AND dossNat.ID_DOSSIER = doss.ID_DOSSIER
				AND dossNat.ID_NATURE = dossNatListe.ID_DOSSIERNATURE
				AND etabInfos.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(DATE_ETABLISSEMENTINFORMATIONS) FROM  etablissementinformations WHERE ID_ETABLISSEMENT = etabDoss.ID_ETABLISSEMENT)
				AND dateComm.ID_DATECOMMISSION = '".$dateCommId."'
				GROUP BY doss.ID_DOSSIER
				ORDER BY etabAdr.NUMINSEE_COMMUNE
			";
		*/
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
			->where('dateComm.ID_DATECOMMISSION = ?',$dateCommId)
			->group('doss.ID_DOSSIER')
			->order('etabAdr.NUMINSEE_COMMUNE');

        return $this->getAdapter()->fetchAll($select);
    }

    public function getDossiersInfosByHour($dateCommId)
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
			->join(array('docurba' => 'dossierdocurba'),'docurba.ID_DOSSIER = doss.ID_DOSSIER')
			->where('dateComm.ID_DATECOMMISSION = ?',$dateCommId)
			->group('doss.ID_DOSSIER')
			->order('dossAffect.HEURE_DEB_AFFECT');

		/*
        $select = "
            SELECT DISTINCT *
            FROM dossier AS doss, datecommission AS dateComm, dossieraffectation AS dossAffect, etablissementdossier AS etabDoss, etablissementinformations as etabInfos, etablissementadresse AS etabAdr, etablissement AS etab, adressecommune AS adrComm, dossiernature AS dossNat, dossiernatureliste AS dossNatListe, dossierdocurba AS dossDocUrba

            WHERE doss.ID_DOSSIER = dossAffect.ID_DOSSIER_AFFECT
            AND dossAffect.ID_DATECOMMISSION_AFFECT = dateComm.ID_DATECOMMISSION
            AND etabDoss.ID_DOSSIER = doss.ID_DOSSIER
            AND etabInfos.ID_ETABLISSEMENT = etabDoss.ID_ETABLISSEMENT
            AND etabDoss.ID_ETABLISSEMENT = etabAdr.ID_ETABLISSEMENT
            AND etabAdr.NUMINSEE_COMMUNE = adrComm.NUMINSEE_COMMUNE
            AND dossNat.ID_DOSSIER = doss.ID_DOSSIER
            AND dossNat.ID_NATURE = dossNatListe.ID_DOSSIERNATURE
            AND etabInfos.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(DATE_ETABLISSEMENTINFORMATIONS) FROM  etablissementinformations WHERE ID_ETABLISSEMENT = etabDoss.ID_ETABLISSEMENT)
            AND dateComm.ID_DATECOMMISSION = '".$dateCommId."'
            GROUP BY doss.ID_DOSSIER
            ORDER BY dossAffect.HEURE_DEB_AFFECT
        ";
		*/
        return $this->getAdapter()->fetchAll($select);
    }

    public function getDossiersInfosByOrder($dateCommId)
    {
		/*
        $select = "
            SELECT DISTINCT *
            FROM dossier AS doss, datecommission AS dateComm, dossieraffectation AS dossAffect, etablissementdossier AS etabDoss, etablissementinformations as etabInfos, etablissementadresse AS etabAdr, etablissement AS etab, adressecommune AS adrComm, dossiernature AS dossNat, dossiernatureliste AS dossNatListe, dossierdocurba AS dossDocUrba

            WHERE doss.ID_DOSSIER = dossAffect.ID_DOSSIER_AFFECT
            AND dossAffect.ID_DATECOMMISSION_AFFECT = dateComm.ID_DATECOMMISSION
            AND etabDoss.ID_DOSSIER = doss.ID_DOSSIER
            AND etabInfos.ID_ETABLISSEMENT = etabDoss.ID_ETABLISSEMENT
            AND etabDoss.ID_ETABLISSEMENT = etabAdr.ID_ETABLISSEMENT
            AND etabAdr.NUMINSEE_COMMUNE = adrComm.NUMINSEE_COMMUNE
            AND dossNat.ID_DOSSIER = doss.ID_DOSSIER
            AND dossNat.ID_NATURE = dossNatListe.ID_DOSSIERNATURE
            AND etabInfos.DATE_ETABLISSEMENTINFORMATIONS = ( SELECT MAX(DATE_ETABLISSEMENTINFORMATIONS) FROM  etablissementinformations WHERE ID_ETABLISSEMENT = etabDoss.ID_ETABLISSEMENT)
            AND dateComm.ID_DATECOMMISSION = '".$dateCommId."'
            GROUP BY doss.ID_DOSSIER
            ORDER BY dossAffect.NUM_DOSSIER
        ";
		*/
		
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
			->join(array('docurba' => 'dossierdocurba'),'docurba.ID_DOSSIER = doss.ID_DOSSIER')
			->where('dateComm.ID_DATECOMMISSION = ?',$dateCommId)
			->group('doss.ID_DOSSIER')
			->order('dossAffect.NUM_DOSSIER');
			
        return $this->getAdapter()->fetchAll($select);
    }

}
