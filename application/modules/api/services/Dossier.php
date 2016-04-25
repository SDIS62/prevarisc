<?php

class Api_Service_Dossier
{
    /**
     * Retourne un seul dossier identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
    public function get($id)
    {
        $service_dossier = new Service_Dossier;
        $dossier = $service_dossier->get($id);
        return $dossier;
    }

    /**
     * Retourne les descriptifs d'un dossier identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
    public function getDescriptifs($id)
    {
        $service_dossier = new Service_Dossier;
        $descriptifs = $service_dossier->getDescriptifs($id);
        return $descriptifs;
    }

    
    /**
     * Retourne les pièces jointes d'un dossier identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
    public function getPiecesJointes($id)
    {
        $service_dossier = new Service_Dossier;
        $pieces_jointes = $service_dossier->getAllPJ($id);
        return $pieces_jointes;
    }
    
    /**
     * Retourne les pièces jointes d'un dossier identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
    public function getPiecesJointesContent($id)
    {
        $service_dossier = new Service_Dossier;
        $pieces_jointes = $service_dossier->getAllPJ($id);

        $store = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getResource('dataStore');
        $pieces_jointes_content = array();
        
        foreach($pieces_jointes as $pieces_jointe) {
            $path = $store->getFilePath($pieces_jointe, 'etablissement', $id);
            $pieces_jointes_content[] = array(
                'ID_PIECE_JOINTE' =>  $pieces_jointe['ID_PIECEJOINTE'],
                'IMAGE' => base64_encode(file_get_contents($path))
            );  
        }

        return $pieces_jointes_content;
    }

    /**
     * Retourne lles contacts d'un dossier identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
    public function getContacts($id)
    {
        $service_dossier = new Service_Dossier;
        $contacts = $service_dossier->getAllContacts($id);
        return $contacts;
    }
    
}