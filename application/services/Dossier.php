<?php

class Service_Dossier
{
    /**
     * Récupération de l'ensemble des types
     *
     * @return array
     */
    public function getAllTypes()
    {
    	$DB_type = new Model_DbTable_DossierType;
    	return $DB_type->fetchAll()->toArray();
    }

    /**
     * Récupération de l'ensemble des natures
     *
     * @return array
     */
    public function getAllNatures()
    {
        $db_nature = new Model_DbTable_DossierNatureliste;
        return $db_nature->fetchAll()->toArray();
    }
    
    
    public function getAllPJ($id_dossier)
    {
        $DBused = new Model_DbTable_PieceJointe;
        return $DBused->affichagePieceJointe("dossierpj", "dossierpj.ID_DOSSIER", $id_dossier);
    }
    
     public function getAllContacts($id_dossier)
    {
        $DB_contact = new Model_DbTable_UtilisateurInformations;

        return $DB_contact->getContact('dossier', $id_dossier);
    }
	
	/**
     * Récupération des textes applicables d'un dossier
     *
     * @param int $id_dossier
     * @return array
     */
    public function getAllTextesApplicables($id_dossier)
    {
		$dossierTextesAppl = new Model_DbTable_DossierTextesAppl;

        $textes_applicables = array();
        $textes_applicables_non_organises = $dossierTextesAppl->recupTextes($id_dossier);

        $old_titre = null;

        foreach($textes_applicables_non_organises as $texte_applicable)
        {
            if(true) {
                $new_titre = $texte_applicable['ID_TYPETEXTEAPPL'];

                if($old_titre != $new_titre && !array_key_exists($texte_applicable['LIBELLE_TYPETEXTEAPPL'], $textes_applicables)) {
                  $textes_applicables[$texte_applicable['LIBELLE_TYPETEXTEAPPL']] = array();
                }

                $textes_applicables[ $texte_applicable['LIBELLE_TYPETEXTEAPPL' ]][$texte_applicable['ID_TEXTESAPPL']] = array(
                  'ID_TEXTESAPPL' => $texte_applicable['ID_TEXTESAPPL'],
                  'LIBELLE_TEXTESAPPL' => $texte_applicable['LIBELLE_TEXTESAPPL'],
                );

                $old_titre = $new_titre;
            }
        }
        return $textes_applicables;
	}
	
	/**
     * Sauvegarde des textes applicables d'un dossier
     *
     * @param int $id_dossier
     * @param array $textes_applicables
     * @return array
     */
    public function saveTextesApplicables($id_dossier, array $textes_applicables)
    {
        $dbDossier = new Model_DbTable_Dossier;
		$dossierTexteApplicable = new Model_DbTable_DossierTextesAppl;	
		$etsTexteApplicable = new Model_DbTable_EtsTextesAppl;
		
		$typeDossier = $dbDossier->getTypeDossier($id_dossier);
		$type = $typeDossier['TYPE_DOSSIER'];
		
		//On récupère le premier établissements afin de mettre à jour ses textes applicables lorsque l'on est dans une visite
		if($type == 2 || $type == 3){
			$tabEtablissement = $dbDossier->getEtablissementDossier($id_dossier);
			$id_etablissement = $tabEtablissement[0]['ID_ETABLISSEMENT'];
		}
		
        foreach($textes_applicables as $id_texte_applicable => $is_active) {
            if(!$is_active) {
                if($dossierTexteApplicable->find($id_texte_applicable, $id_dossier)->current() !== null) {
                    $dossierTexteApplicable->find($id_texte_applicable, $id_dossier)->current()->delete();
					if($type == 2 || $type == 3){
						$etsTexteApplicable->find($id_texte_applicable, $id_etablissement)->current()->delete();
					}
                }
            }
            else {
                if($dossierTexteApplicable->find($id_texte_applicable, $id_dossier)->current() === null) {
                    $row = $dossierTexteApplicable->createRow();
                    $row->ID_TEXTESAPPL = $id_texte_applicable;
                    $row->ID_DOSSIER = $id_dossier;
                    $row->save();
					if($type == 2 || $type == 3){
						$row = $etsTexteApplicable->createRow();
						$row->ID_TEXTESAPPL = $id_texte_applicable;
						$row->ID_ETABLISSEMENT = $id_etablissement;
						$row->save();
					}
                }
            }
        }
    }
	
	
}
