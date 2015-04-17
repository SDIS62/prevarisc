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
        $DB_type = new Model_DbTable_DossierType();

        return $DB_type->fetchAll()->toArray();
    }

    /**
     * Récupération de l'ensemble des natures
     *
     * @return array
     */
    public function getAllNatures()
    {
        $db_nature = new Model_DbTable_DossierNatureliste();

        return $db_nature->fetchAll()->toArray();
    }

    /**
     * Récupération des pièces jointes d'un dossier
     *
     * @param  int   $id_dossier
     * @return array
     */
    public function getAllPJ($id_dossier)
    {
        $DBused = new Model_DbTable_PieceJointe();

        return $DBused->affichagePieceJointe("dossierpj", "dossierpj.ID_DOSSIER", $id_dossier);
    }

    /**
     * Ajout d'une pièce jointe pour un dossier
     *
     * @param int    $id_dossier
     * @param array  $file
     * @param string $name
     * @param string $description
     */
    public function addPJ($id_dossier, $file, $name = '', $description = '')
    {
        $extension = strtolower(strrchr($file['name'], "."));

        $DBpieceJointe = new Model_DbTable_PieceJointe();

        $piece_jointe = array(
            'EXTENSION_PIECEJOINTE' => $extension,
            'NOM_PIECEJOINTE' => '' == $name ? substr($file['name'], 0, -4) : $name,
            'DESCRIPTION_PIECEJOINTE' => $description,
            'DATE_PIECEJOINTE' => date('Y-m-d'),
        );

        $piece_jointe['ID_PIECEJOINTE'] = $DBpieceJointe->createRow($piece_jointe)->save();

        $store = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('dataStore');
        $file_path = $store->getFilePath($piece_jointe, 'dossier', $id_dossier, true);

        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            $msg = 'Ne peut pas déplacer le fichier '.$file['tmp_name'].' vers '.$file_path;

            // log some debug information
            error_log($msg);
            error_log("is_dir ".dirname($file_path).": ".is_dir(dirname($file_path)));
            error_log("is_writable ".dirname($file_path).":".is_writable(dirname($file_path)));
            $cmd = 'ls -all '.dirname($file_path);
            error_log($cmd);
            $rslt = explode("\n", shell_exec($cmd));
            foreach ($rslt as $file) {
                error_log($file);
            }

            throw new Exception($msg);
        } else {
            $DBsave = new Model_DbTable_DossierPj();
            $DBetab = new Model_DbTable_EtablissementPj();

            $linkPj = $DBsave->createRow(array(
                'ID_DOSSIER' => $id_dossier,
                'ID_PIECEJOINTE' => $piece_jointe['ID_PIECEJOINTE'],
            ))->save();

            /*
            if ($this->_getParam('etab')) {
                foreach ($this->_getParam('etab') as $etabLink ) {
                    $linkEtab = $DBetab->createRow();
                    $linkEtab->ID_ETABLISSEMENT = $etabLink;
                    $linkEtab->ID_PIECEJOINTE = $nouvellePJ->ID_PIECEJOINTE;
                    $linkEtab->save();
                }
            }
            */
        }
    }

    /**
     * Suppression d'une pièce jointe d'un dossier
     *
     * @param int $id_dossier
     * @param int $id_pj
     */
    public function deletePJ($id_dossier, $id_pj)
    {
        $DBpieceJointe = new Model_DbTable_PieceJointe();
        $DBitem = new Model_DbTable_DossierPj();

        $pj = $DBpieceJointe->find($id_pj)->current();
        if (!$pj) {
            return;
        }

        $store = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('dataStore');
        $file_path = $store->getFilePath($pj, 'dossier', $id_dossier);

        if ($DBitem != null) {
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $DBitem->delete("ID_PIECEJOINTE = ".(int) $id_pj);
            $pj->delete();
        }
    }

    /**
     * Récupération des contacts d'un dossier
     *
     * @param  int   $id_dossier
     * @return array
     */
    public function getAllContacts($id_dossier)
    {
        $DB_contact = new Model_DbTable_UtilisateurInformations();

        return $DB_contact->getContact('dossier', $id_dossier);
    }

    /**
     * Récupération des textes applicables d'un dossier
     *
     * @param  int   $id_dossier
     * @return array
     */
    public function getAllTextesApplicables($id_dossier)
    {
        $dossierTextesAppl = new Model_DbTable_DossierTextesAppl();

        $textes_applicables = array();
        $textes_applicables_non_organises = $dossierTextesAppl->recupTextes($id_dossier);

        $old_titre = null;

        foreach ($textes_applicables_non_organises as $texte_applicable) {
            if (true) {
                $new_titre = $texte_applicable['ID_TYPETEXTEAPPL'];

                if ($old_titre != $new_titre && !array_key_exists($texte_applicable['LIBELLE_TYPETEXTEAPPL'], $textes_applicables)) {
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
     * @param  int   $id_dossier
     * @param  array $textes_applicables
     * @return array
     */
    public function saveTextesApplicables($id_dossier, array $textes_applicables)
    {
        $dbDossier = new Model_DbTable_Dossier();
        $dossierTexteApplicable = new Model_DbTable_DossierTextesAppl();
        $etsTexteApplicable = new Model_DbTable_EtsTextesAppl();

        $typeDossier = $dbDossier->getTypeDossier($id_dossier);
        $type = $typeDossier['TYPE_DOSSIER'];

        //On récupère le premier établissements afin de mettre à jour ses textes applicables lorsque l'on est dans une visite
        if (2 == $type || 3 == $type) {
            $tabEtablissement = $dbDossier->getEtablissementDossier($id_dossier);
            $id_etablissement = $tabEtablissement[0]['ID_ETABLISSEMENT'];
        }

        foreach ($textes_applicables as $id_texte_applicable => $is_active) {
            if (!$is_active) {
                if ($dossierTexteApplicable->find($id_texte_applicable, $id_dossier)->current() !== null) {
                    $dossierTexteApplicable->find($id_texte_applicable, $id_dossier)->current()->delete();
                    if (2 == $type || 3 == $type) {
                        $etsTexteApplicable->find($id_texte_applicable, $id_etablissement)->current()->delete();
                    }
                }
            } else {
                if ($dossierTexteApplicable->find($id_texte_applicable, $id_dossier)->current() === null) {
                    $row = $dossierTexteApplicable->createRow();
                    $row->ID_TEXTESAPPL = $id_texte_applicable;
                    $row->ID_DOSSIER = $id_dossier;
                    $row->save();
                    if (2 == $type || 3 == $type) {
                        $exist = $etsTexteApplicable->find($id_texte_applicable,$id_etablissement)->current();
                        if (! $exist) {
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

    /**
     * Ajout d'un contact à un dossier
     *
     * @param int    $id_dossier
     * @param string $nom
     * @param string $prenom
     * @param int    $id_fonction
     * @param string $societe
     * @param string $fixe
     * @param string $mobile
     * @param string $fax
     * @param string $email
     * @param string $adresse
     * @param string $web
     */
    public function addContact($id_dossier, $nom, $prenom, $id_fonction, $societe, $fixe, $mobile, $fax, $email, $adresse, $web)
    {
        $DB_informations = new Model_DbTable_UtilisateurInformations();

        $id_contact = $DB_informations->createRow(array(
            'NOM_UTILISATEURINFORMATIONS' => (string) $nom,
            'PRENOM_UTILISATEURINFORMATIONS' => (string) $prenom,
            'TELFIXE_UTILISATEURINFORMATIONS' => (string) $fixe,
            'TELPORTABLE_UTILISATEURINFORMATIONS' => (string) $mobile,
            'TELFAX_UTILISATEURINFORMATIONS' => (string) $fax,
            'MAIL_UTILISATEURINFORMATIONS' => (string) $email,
            'SOCIETE_UTILISATEURINFORMATIONS' => (string) $societe,
            'WEB_UTILISATEURINFORMATIONS' => (string) $web,
            'OBS_UTILISATEURINFORMATIONS' => (string) $adresse,
            'ID_FONCTION' => (string) $id_fonction,
        ))->save();

        $this->addContactExistant($id_dossier, $id_contact);
    }

    /**
     * Ajout d'un contact existant à un dossier
     *
     * @param int $id_dossier
     * @param int $id_contact
     */
    public function addContactExistant($id_dossier, $id_contact)
    {
        $DB_contact = new Model_DbTable_DossierContact();

        $DB_contact->createRow(array(
            'ID_DOSSIER' => $id_dossier,
            'ID_UTILISATEURINFORMATIONS' => $id_contact,
        ))->save();
    }

    /**
     * Suppression d'un contact
     *
     * @param int $id_dossier
     * @param int $id_contact
     */
    public function deleteContact($id_dossier, $id_contact)
    {
        $DB_current = new Model_DbTable_EtablissementContact();
        $DB_informations = new Model_DbTable_UtilisateurInformations();
        $DB_contact = array(
            new Model_DbTable_EtablissementContact(),
            new Model_DbTable_DossierContact(),
            new Model_DbTable_GroupementContact(),
            new Model_DbTable_CommissionContact(),
        );

        // Appartient à d'autre dossier / ets ?
        $exist = false;
        foreach ($DB_contact as $key => $model) {
            if (count($model->fetchAll("ID_UTILISATEURINFORMATIONS = ".$id_contact)->toArray()) > (($model == $DB_current) ? 1 : 0)) {
                $exist = true;
            }
        }

        // Est ce que le contact n'appartient pas à d'autre etablissement ?
        if (!$exist) {
            $DB_current->delete("ID_UTILISATEURINFORMATIONS = ".$id_contact); // Porteuse
            $DB_informations->delete("ID_UTILISATEURINFORMATIONS = ".$id_contact); // Contact
        } else {
            $DB_current->delete("ID_UTILISATEURINFORMATIONS = ".$id_contact." AND ID_DOSSIER = ".$id_dossier); // Porteuse
        }
    }

    /**
     * Retourne les prescriptions d'un dossier
     *
     * @param  int   $id_dossier
     * @return array
     */
    public function getPrescriptions($id_dossier,$type)
    {
        /* 
            suivant la valeur de $type
            0 = rappel réglementaire
            1 = a l'exploitation
            2 = a l'amélioration
        */
        $dbPrescDossier = new Model_DbTable_PrescriptionDossier();
        $listePrescDossier = $dbPrescDossier->recupPrescDossier($id_dossier,$type);

        $dbPrescDossierAssoc = new Model_DbTable_PrescriptionDossierAssoc();
        $prescriptionArray = array();
        foreach ($listePrescDossier as $val => $ue) {
            if ($ue['ID_PRESCRIPTION_TYPE']) {
                //cas d'une prescription type
                $assoc = $dbPrescDossierAssoc->getPrescriptionTypeAssoc($ue['ID_PRESCRIPTION_TYPE'],$ue['ID_PRESCRIPTION_DOSSIER']);
                array_push($prescriptionArray, $assoc);
            } else {
                //cas d'une prescription particulière
                $assoc = $dbPrescDossierAssoc->getPrescriptionDossierAssoc($ue['ID_PRESCRIPTION_DOSSIER']);
                array_push($prescriptionArray, $assoc);
            }
        }

        return $prescriptionArray;
    }

    /**
     * Retourne les détails d'une prescription
     *
     * @param  int   $id_prescription
     * @return array
     */
    public function getDetailPrescription($id_prescription)
    {
        //On recherche la ligne correspondante à la prescription
        $db_prescription_dossier = new Model_DbTable_PrescriptionDossier();
        $infos_prescription = $db_prescription_dossier->recupPrescInfos($id_prescription);

        //On va chercher les textes et articles associés à cette prescription
        if (NULL == $infos_prescription['ID_PRESCRIPTION_TYPE']) {
            $db_prescription_assoc = new Model_DbTable_PrescriptionDossierAssoc();
            $liste_assoc = $db_prescription_assoc->getPrescriptionDossierAssoc($id_prescription);
            $infos_prescription['assoc'] = $liste_assoc;
        } else {
            $dbPrescTypeAssoc = new Model_DbTable_PrescriptionTypeAssoc();

            $liste_assoc = $dbPrescTypeAssoc->getPrescriptionAssoc($infos_prescription['ID_PRESCRIPTION_TYPE']);
            $infos_prescription['assoc'] = $liste_assoc;
            $infos_prescription['LIBELLE_PRESCRIPTION_DOSSIER'] = $liste_assoc[0]['PRESCRIPTIONTYPE_LIBELLE'];
        }

        return $infos_prescription;
    }

    /**
     * Sauvegarde une prescription
     *
     * @param array $post
     *
     */
    public function savePrescription($post)
    {
        $dbPrescDossier = new Model_DbTable_PrescriptionDossier();
        $dbPrescDossierAssoc = new Model_DbTable_PrescriptionDossierAssoc();

        if ('edit' == $post['action']) {
            $prescEdit = $dbPrescDossier->find($post['id_prescription'])->current();

            if( $prescEdit->TYPE_PRESCRIPTION_DOSSIER != $post['TYPE_PRESCRIPTION_DOSSIER'] ){
                $nbPrescription = $dbPrescDossier->recupMaxNumPrescDossier($post['id_dossier'],$post['TYPE_PRESCRIPTION_DOSSIER']);
                $numPrescription = $nbPrescription['maxnum'];
                $numPrescription++;

                $oldType = $prescEdit->TYPE_PRESCRIPTION_DOSSIER;
                $newCount = true;
            }else{
                $numPrescription = $prescEdit->NUM_PRESCRIPTION_DOSSIER;
                $newCount = false;
            }

            $prescEdit->NUM_PRESCRIPTION_DOSSIER = $numPrescription;
            $prescEdit->LIBELLE_PRESCRIPTION_DOSSIER = $post['PRESCRIPTION_LIBELLE'];
            $prescEdit->TYPE_PRESCRIPTION_DOSSIER = $post['TYPE_PRESCRIPTION_DOSSIER'];
            $prescEdit->save();

            if($newCount == true){
                //il faut effectuer une nouvelle numérotation des prescriptions du type que l'on abandonne
                $nbPresc = 1;
                $listeExploit = $dbPrescDossier->recupPrescDossier($post['id_dossier'], 0);
                foreach($listeExploit as $prescDossier){
                    $prescCount = $dbPrescDossier->find($prescDossier['ID_PRESCRIPTION_DOSSIER'])->current();
                    $prescCount->NUM_PRESCRIPTION_DOSSIER = $nbPresc;
                    $prescCount->save();
                    $nbPresc++;
                }

                $nbPresc = 1;
                $listeExploit = $dbPrescDossier->recupPrescDossier($post['id_dossier'], 1);                
                foreach($listeExploit as $prescDossier){
                    $prescCount = $dbPrescDossier->find($prescDossier['ID_PRESCRIPTION_DOSSIER'])->current();
                    $prescCount->NUM_PRESCRIPTION_DOSSIER = $nbPresc;
                    $prescCount->save();
                    $nbPresc++;
                }

                $listeAmelio = $dbPrescDossier->recupPrescDossier($post['id_dossier'], 2);
                foreach($listeAmelio as $prescDossier){
                    $prescCount = $dbPrescDossier->find($prescDossier['ID_PRESCRIPTION_DOSSIER'])->current();
                    $prescCount->NUM_PRESCRIPTION_DOSSIER = $nbPresc;
                    $prescCount->save();
                    $nbPresc++;
                }
            }

            $prescAssocDelete = $dbPrescDossierAssoc->getAdapter()->quoteInto('ID_PRESCRIPTION_DOSSIER = ?', $prescEdit->ID_PRESCRIPTION_DOSSIER);
            $dbPrescDossierAssoc->delete($prescAssocDelete);

            $nombreAssoc = count($post['texte']);
            for ($i = 0; $i< $nombreAssoc; $i ++) {
                $newAssoc = $dbPrescDossierAssoc->createRow();
                $newAssoc->NUM_PRESCRIPTION_DOSSIERASSOC = $i + 1;
                $newAssoc->ID_PRESCRIPTION_DOSSIER = $post['id_prescription'];
                if($post['texte'][$i] != null && $post['texte'][$i] != '' && $post['texte'][$i] != 0){
                    $newAssoc->ID_TEXTE = $post['texte'][$i];
                }else{
                    $newAssoc->ID_TEXTE = 1;
                }
                if($post['article'][$i] != null && $post['article'][$i] != '' && $post['article'][$i] != 0){
                    $newAssoc->ID_ARTICLE = $post['article'][$i];
                }else{
                    $newAssoc->ID_ARTICLE = 1;
                }
                $newAssoc->save();
            }
        } elseif ('edit-type' == $post['action']) {
            $prescEdit = $dbPrescDossier->find($post['id_prescription'])->current();

            if($prescEdit->TYPE_PRESCRIPTION_DOSSIER != $post['TYPE_PRESCRIPTION_DOSSIER']){
                $nbPrescription = $dbPrescDossier->recupMaxNumPrescDossier($post['id_dossier'],$post['TYPE_PRESCRIPTION_DOSSIER']);
                $numPrescription = $nbPrescription['maxnum'];
                $numPrescription++;
            }else{
                $numPrescription = $prescEdit->NUM_PRESCRIPTION_DOSSIER;
            }

            $prescEdit->LIBELLE_PRESCRIPTION_DOSSIER = $post['PRESCRIPTION_LIBELLE'];
            $prescEdit->ID_PRESCRIPTION_TYPE = null;
            $prescEdit->NUM_PRESCRIPTION_DOSSIER = $numPrescription;
            $prescEdit->TYPE_PRESCRIPTION_DOSSIER = $post['TYPE_PRESCRIPTION_DOSSIER'];
            $prescEdit->save();

            $nombreAssoc = count($post['texte']);
            for ($i = 0; $i< $nombreAssoc; $i ++) {
                $newAssoc = $dbPrescDossierAssoc->createRow();
                $newAssoc->NUM_PRESCRIPTION_DOSSIERASSOC = $i + 1;
                $newAssoc->ID_PRESCRIPTION_DOSSIER = $post['id_prescription'];
                $newAssoc->ID_TEXTE = $post['texte'][$i];
                $newAssoc->ID_ARTICLE = $post['article'][$i];
                $newAssoc->save();
            }

            $nbPresc = 1;
            $listeExploit = $dbPrescDossier->recupPrescDossier($post['id_dossier'], 1);                
            foreach($listeExploit as $prescDossier){
                $prescCount = $dbPrescDossier->find($prescDossier['ID_PRESCRIPTION_DOSSIER'])->current();
                $prescCount->NUM_PRESCRIPTION_DOSSIER = $nbPresc;
                $prescCount->save();
                $nbPresc++;
            }

            $listeAmelio = $dbPrescDossier->recupPrescDossier($post['id_dossier'], 2);
            foreach($listeAmelio as $prescDossier){
                $prescCount = $dbPrescDossier->find($prescDossier['ID_PRESCRIPTION_DOSSIER'])->current();
                $prescCount->NUM_PRESCRIPTION_DOSSIER = $nbPresc;
                $prescCount->save();
                $nbPresc++;
            }

        } elseif ('presc-add' == $post['action']) {

            $nbPrescription = $dbPrescDossier->recupMaxNumPrescDossier($post['id_dossier'],$post['TYPE_PRESCRIPTION_DOSSIER']);
            $numPrescription = $nbPrescription['maxnum'];
            $numPrescription++;

            $prescEdit = $dbPrescDossier->createRow();
            $prescEdit->ID_DOSSIER = $post['id_dossier'];
            $prescEdit->NUM_PRESCRIPTION_DOSSIER = $numPrescription;
            $prescEdit->LIBELLE_PRESCRIPTION_DOSSIER = $post['PRESCRIPTION_LIBELLE'];
            $prescEdit->TYPE_PRESCRIPTION_DOSSIER = $post['TYPE_PRESCRIPTION_DOSSIER'];
            $prescEdit->save();
            
            $nombreAssoc = count($post['texte']);
            for ($i = 0; $i< $nombreAssoc; $i ++) {
                $newAssoc = $dbPrescDossierAssoc->createRow();
                $newAssoc->NUM_PRESCRIPTION_DOSSIERASSOC = $i + 1;
                $newAssoc->ID_PRESCRIPTION_DOSSIER = $prescEdit->ID_PRESCRIPTION_DOSSIER;
                if($post['texte'][$i] != null && $post['texte'][$i] != '' && $post['texte'][$i] != 0){
                    $newAssoc->ID_TEXTE = $post['texte'][$i];
                }else{
                    $newAssoc->ID_TEXTE = 1;
                }

                if($post['article'][$i] != null && $post['article'][$i] != '' && $post['article'][$i] != 0){
                    $newAssoc->ID_ARTICLE = $post['article'][$i];
                }else{
                    $newAssoc->ID_ARTICLE = 1;
                }
                $newAssoc->save();
            }

            $nbPresc = 1;
            $listeExploit = $dbPrescDossier->recupPrescDossier($post['id_dossier'], 1);
            foreach($listeExploit as $prescDossier){
                $prescCount = $dbPrescDossier->find($prescDossier['ID_PRESCRIPTION_DOSSIER'])->current();
                $prescCount->NUM_PRESCRIPTION_DOSSIER = $nbPresc;
                $prescCount->save();
                $nbPresc++;
            }

            $listeAmelio = $dbPrescDossier->recupPrescDossier($post['id_dossier'], 2);
            foreach($listeAmelio as $prescDossier){
                $prescCount = $dbPrescDossier->find($prescDossier['ID_PRESCRIPTION_DOSSIER'])->current();
                $prescCount->NUM_PRESCRIPTION_DOSSIER = $nbPresc;
                $prescCount->save();
                $nbPresc++;
            }
        }
        $id_dossier = $post['id_dossier'];
    }


    public function copyPrescriptionDossier($listePrescription,$idDossier)
    {
        $dbPrescDossier = new Model_DbTable_PrescriptionDossier();
        $dbPrescDossierAssoc = new Model_DbTable_PrescriptionDossierAssoc();

        foreach ($listePrescription as $val => $ue) {
            if (isset($ue[0]['ID_PRESCRIPTION_TYPE']) && $ue[0]['ID_PRESCRIPTION_TYPE'] != NULL) {
                //cas d'une prescription type
                $assoc = $dbPrescDossierAssoc->getPrescriptionTypeAssoc($ue[0]['ID_PRESCRIPTION_TYPE'],$ue[0]['ID_PRESCRIPTION_DOSSIER']);
            } else {
                //cas d'une prescription particulière
                $assoc = $dbPrescDossierAssoc->getPrescriptionDossierAssoc($ue[0]['ID_PRESCRIPTION_DOSSIER']);
            }

            $newPresc = $dbPrescDossier->createRow();
            $newPresc->ID_DOSSIER = $idDossier;
            $newPresc->NUM_PRESCRIPTION_DOSSIER = $ue[0]["NUM_PRESCRIPTION_DOSSIER"];
            $newPresc->ID_PRESCRIPTION_TYPE = $ue[0]["ID_PRESCRIPTION_TYPE"];
            $newPresc->LIBELLE_PRESCRIPTION_DOSSIER = $ue[0]["LIBELLE_PRESCRIPTION_DOSSIER"];
            $newPresc->TYPE_PRESCRIPTION_DOSSIER = $ue[0]["TYPE_PRESCRIPTION_DOSSIER"];
            $newPresc->save();

            foreach ($assoc as $val) {
                if ($val["ID_PRESCRIPTION_TYPE"] == NULL) {
                    $newAssoc = $dbPrescDossierAssoc->createRow();
                    $newAssoc->NUM_PRESCRIPTION_DOSSIERASSOC = $val["NUM_PRESCRIPTION_DOSSIERASSOC"];
                    $newAssoc->ID_PRESCRIPTION_DOSSIER = $newPresc->ID_PRESCRIPTION_DOSSIER;
                    $newAssoc->ID_TEXTE = $val["ID_TEXTE"];
                    $newAssoc->ID_ARTICLE = $val["ID_ARTICLE"];
                    $newAssoc->save();
                }
            }

        }

    }

    /**
     * Supprime une prescription
     *
     * @param array $post
     *
     */
    public function deletePrescription($post)
    {
        $dbPrescDossier = new Model_DbTable_PrescriptionDossier();
        $dbPrescDossierAssoc = new Model_DbTable_PrescriptionDossierAssoc();

        $prescToDelete = $dbPrescDossier->find($post['id_prescription'])->current();

        $prescAssocDelete = $dbPrescDossierAssoc->getAdapter()->quoteInto('ID_PRESCRIPTION_DOSSIER = ?', $prescToDelete->ID_PRESCRIPTION_DOSSIER);
        $dbPrescDossierAssoc->delete($prescAssocDelete);
        $prescToDelete->delete();

        $prescriptionDossier = $dbPrescDossier->recupPrescDossier($post['id_dossier'],0);
        $num = 1;
        foreach ($prescriptionDossier as $val => $ue) {
            $prescChangePlace = $dbPrescDossier->find($ue['ID_PRESCRIPTION_DOSSIER'])->current();
            $prescChangePlace->NUM_PRESCRIPTION_DOSSIER = $num;
            $prescChangePlace->save();
            $num++;
        }

        $prescriptionDossier = $dbPrescDossier->recupPrescDossier($post['id_dossier'],1);
        $num = 1;
        foreach ($prescriptionDossier as $val => $ue) {
            $prescChangePlace = $dbPrescDossier->find($ue['ID_PRESCRIPTION_DOSSIER'])->current();
            $prescChangePlace->NUM_PRESCRIPTION_DOSSIER = $num;
            $prescChangePlace->save();
            $num++;
        }

        $prescriptionDossier = $dbPrescDossier->recupPrescDossier($post['id_dossier'],2);
        foreach ($prescriptionDossier as $val => $ue) {
            $prescChangePlace = $dbPrescDossier->find($ue['ID_PRESCRIPTION_DOSSIER'])->current();
            $prescChangePlace->NUM_PRESCRIPTION_DOSSIER = $num;
            $prescChangePlace->save();
            $num++;
        }
    }

    public function savePrescriptionRegl($idDossier, $prescriptionRegl)
    {
        $dbPrescDossier = new Model_DbTable_PrescriptionDossier();
        $dbPrescDossierAssoc = new Model_DbTable_PrescriptionDossierAssoc();
        foreach($prescriptionRegl as $val => $ue){
            $prescEdit = $dbPrescDossier->createRow();
            $prescEdit->ID_DOSSIER = $idDossier;
            $prescEdit->LIBELLE_PRESCRIPTION_DOSSIER = $ue[0]['PRESCRIPTIONREGL_LIBELLE'];
            $prescEdit->TYPE_PRESCRIPTION_DOSSIER = 0;
            $prescEdit->save();

            $nombreAssoc = count($ue);
            for ($i = 0; $i< $nombreAssoc; $i ++) {
                $newAssoc = $dbPrescDossierAssoc->createRow();
                $newAssoc->NUM_PRESCRIPTION_DOSSIERASSOC = $i + 1;
                $newAssoc->ID_PRESCRIPTION_DOSSIER = $prescEdit->ID_PRESCRIPTION_DOSSIER;
                $newAssoc->ID_TEXTE = $ue[$i]['ID_TEXTE'];
                $newAssoc->ID_ARTICLE = $ue[$i]['ID_ARTICLE'];
                $newAssoc->save();
            }
        }
    }


    public function changePosPrescription($tabId){
        $DBprescDossier = new Model_DbTable_PrescriptionDossier();

        $numPresc = 1;
        foreach ($tabId as $idPrescDoss) {
            $updatePrescDossier = $DBprescDossier->find($idPrescDoss)->current();
            $updatePrescDossier->NUM_PRESCRIPTION_DOSSIER = $numPresc;
            $updatePrescDossier->save();
            $numPresc++;
        }
    }

    public function getEtabInfos($id_dossier = null, $id_etablissement = null){
        $DBdossier = new Model_DbTable_Dossier();

        if ($id_etablissement != null) {

            $DBetab = new Model_DbTable_Etablissement();
            $etabTab = $DBetab->getInformations($id_etablissement);

            $this->etablissement = $etabTab->toArray();

            $DbAdresse = new Model_DbTable_EtablissementAdresse();
            $this->etablissement['adresses'] = $DbAdresse->get($id_etablissement);



            $service_etablissement = new Service_Etablissement();
            $etablissementInfos = $service_etablissement->get($id_etablissement);
            if ($etablissementInfos['general']['ID_DOSSIER_DONNANT_AVIS'] != null) {
                $etablissementInfos['avisExploitation'] = $DBdossier->getAvisDossier($etablissementInfos['general']['ID_DOSSIER_DONNANT_AVIS']);
            }
            $this->etablissement['etablissementInfos'] = $etablissementInfos;

            if ($this->etablissement['etablissementInfos']['general']['ID_DOSSIER_DONNANT_AVIS'] != null) {
                $avisExploitationEtab = $DBdossier->getAvisDossier($this->etablissement['etablissementInfos']['general']['ID_DOSSIER_DONNANT_AVIS']);
                $this->etablissement['avisExploitationEtab'] = $avisExploitationEtab['AVIS_DOSSIER'];
            } else {
                $this->etablissement['avisExploitationEtab'] = 3;
            }

            return $this->etablissement;

        } elseif ($id_dossier != null){
            $tabEtablissement = $DBdossier->getEtablissementDossier((int) $id_dossier);
            $this->listeEtablissement = $tabEtablissement;

            $service_etablissement = new Service_Etablissement();
            foreach ($this->listeEtablissement as $val => $ue) {
                $etablissementInfos = $service_etablissement->get($ue['ID_ETABLISSEMENT']);
                if ($etablissementInfos['general']['ID_DOSSIER_DONNANT_AVIS'] != null) {
                    $this->listeEtablissement[$val]['avisExploitation'] = $DBdossier->getAvisDossier($etablissementInfos['general']['ID_DOSSIER_DONNANT_AVIS']);
                }
                $this->listeEtablissement[$val]['infosEtab'] = $etablissementInfos;
            }

            return $this->listeEtablissement;
        }



    }

}
