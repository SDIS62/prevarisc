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

    /**
     * Récupération des pièces jointes d'un dossier
     *
     * @param int $id_dossier
     * @return array
     */
    public function getAllPJ($id_dossier)
    {
        $DBused = new Model_DbTable_PieceJointe;
        return $DBused->affichagePieceJointe("dossierpj", "dossierpj.ID_DOSSIER", $id_dossier);
    }

    /**
     * Ajout d'une pièce jointe pour un dossier
     *
     * @param int $id_dossier
     * @param array $file
     * @param string $name
     * @param string $description
     */
    public function addPJ($id_dossier, $file, $name = '', $description = '')
    {
        $extension = strtolower(strrchr($file['name'], "."));

        $DBpieceJointe = new Model_DbTable_PieceJointe;

        $piece_jointe = array(
            'EXTENSION_PIECEJOINTE' => $extension,
            'NOM_PIECEJOINTE' => $name == '' ? substr($file['name'], 0, -4) : $name,
            'DESCRIPTION_PIECEJOINTE' => $description,
            'DATE_PIECEJOINTE' => date('Y-m-d')
        );

        $piece_jointe['ID_PIECEJOINTE'] = $DBpieceJointe->createRow($piece_jointe)->save();

        $store = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('dataStore');
        $file_path = $store->getFilePath($piece_jointe, 'dossier', $id_dossier, true);

        if(!move_uploaded_file($file['tmp_name'], $file_path)) {
            $msg = 'Ne peut pas déplacer le fichier ' . $file['tmp_name']. ' vers '.$file_path;

            // log some debug information
            error_log($msg);
            error_log("is_dir ".dirname($file_path).": ".is_dir(dirname($file_path)));
            error_log("is_writable ".dirname($file_path).":".is_writable(dirname($file_path)));
            $cmd = 'ls -all '.dirname($file_path);
            error_log($cmd);
            $rslt = explode("\n", shell_exec($cmd));
            foreach($rslt as $file) {
                error_log($file);
            }

            throw new Exception($msg);
        }
        else {
            $DBsave = new Model_DbTable_DossierPj;
            $DBetab = new Model_DbTable_EtablissementPj;

            $linkPj = $DBsave->createRow(array(
                'ID_DOSSIER' => $id_dossier,
                'ID_PIECEJOINTE' => $piece_jointe['ID_PIECEJOINTE']
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
        $DBpieceJointe = new Model_DbTable_PieceJointe;
        $DBitem = new Model_DbTable_DossierPj;

        $pj = $DBpieceJointe->find($id_pj)->current();
        if (!$pj) {
            return ;
        }

        $store = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('dataStore');
        $file_path = $store->getFilePath($pj, 'dossier', $id_dossier);

        if ($DBitem != null) {
            if( file_exists($file_path) )         unlink($file_path);
            $DBitem->delete("ID_PIECEJOINTE = " . (int) $id_pj);
            $pj->delete();
        }
    }

    /**
     * Récupération des contacts d'un dossier
     *
     * @param int $id_dossier
     * @return array
     */
    public function getAllContacts($id_dossier)
    {
        $DB_contact = new Model_DbTable_UtilisateurInformations;

        return $DB_contact->getContact('dossier', $id_dossier);
    }

    /**
     * Ajout d'un contact à un dossier
     *
     * @param int $id_dossier
     * @param string $nom
     * @param string $prenom
     * @param int $id_fonction
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
        $DB_informations = new Model_DbTable_UtilisateurInformations;

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
            'ID_FONCTION' => (string) $id_fonction
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
        $DB_contact = new Model_DbTable_DossierContact;

        $DB_contact->createRow(array(
            'ID_DOSSIER' => $id_dossier,
            'ID_UTILISATEURINFORMATIONS' => $id_contact
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
        $DB_current = new Model_DbTable_EtablissementContact;
        $DB_informations = new Model_DbTable_UtilisateurInformations;
        $DB_contact = array(
            new Model_DbTable_EtablissementContact,
            new Model_DbTable_DossierContact,
            new Model_DbTable_GroupementContact,
            new Model_DbTable_CommissionContact
        );

        // Appartient à d'autre dossier / ets ?
        $exist = false;
        foreach ($DB_contact as $key => $model) {
            if (count($model->fetchAll("ID_UTILISATEURINFORMATIONS = " . $id_contact)->toArray()) > (($model == $DB_current) ? 1 : 0) ) {
                $exist = true;
            }
        }

        // Est ce que le contact n'appartient pas à d'autre etablissement ?
        if (!$exist) {
            $DB_current->delete("ID_UTILISATEURINFORMATIONS = " . $id_contact); // Porteuse
            $DB_informations->delete( "ID_UTILISATEURINFORMATIONS = " . $id_contact ); // Contact
        } else {
            $DB_current->delete("ID_UTILISATEURINFORMATIONS = " . $id_contact . " AND ID_DOSSIER = " . $id_dossier); // Porteuse
        }
    }
}
