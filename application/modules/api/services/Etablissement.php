<?php

class Api_Service_Etablissement
{
    /**
     * Retourne un seul établissement identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
	public function get($id)
	{
		$service_etablissement = new Service_Etablissement;
		$etablissement = $service_etablissement->get($id);
        return $etablissement;
    }

    /**
     * Retourne l'historique complet d'un établissement identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
	public function getHistorique($id)
	{
		$service_etablissement = new Service_Etablissement;
		$historique = $service_etablissement->getHistorique($id);
        return $historique;
    }

    /**
     * Retourne les descriptifs d'un établissement identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
	public function getDescriptifs($id)
	{
		$service_etablissement = new Service_Etablissement;
		$descriptifs = $service_etablissement->getDescriptifs($id);
        return $descriptifs;
    }

    /**
     * Retourne les textes applicables d'un établissement identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
    public function getTextesApplicables($id)
    {
            $service_etablissement = new Service_Etablissement;
            $textes_applicables = $service_etablissement->getAllTextesApplicables($id);
            return $textes_applicables;
    }

    /**
     * Retourne les pièces jointes d'un établissement identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
    public function getPiecesJointes($id)
    {
        $service_etablissement = new Service_Etablissement;
        $pieces_jointes = $service_etablissement->getAllPJ($id);
        return $pieces_jointes;
    }
    
    /**
     * Retourne les pièces jointes d'un établissement identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
    public function getPiecesJointesContent($id)
    {
        $service_etablissement = new Service_Etablissement;
        $pieces_jointes = $service_etablissement->getAllPJ($id);
        
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
     * Retourne lles contacts d'un établissement identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
	public function getContacts($id)
	{
		$service_etablissement = new Service_Etablissement;
		$contacts = $service_etablissement->getAllContacts($id);
        return $contacts;
    }

    /**
     * Retourne les dossiers d'un établissement identifié par le paramètre id.
     *
     * @param int $id
     * @return string
     */
	public function getDossiers($id)
	{
		$service_etablissement = new Service_Etablissement;
		$dossiers = $service_etablissement->getDossiers($id);
        return $dossiers;
    }

    /**
     * Retourne les valeurs par défauts (périodicité, commission, préventionnistes) pour un établissement en fonction des paramètres données.
     *
     * @param int $genre
     * @param int $numinsee
     * @param int $type
     * @param int $categorie
     * @param bool $local_sommeil
     * @param int $classe
     * @param int $id_etablissement_pere
     * @param array $ids_etablissements_enfants
     * @return string
     */
    public function getDefaultValues($genre, $numinsee = null, $type = null, $categorie = null, $local_sommeil = null, $classe = null, $id_etablissement_pere = null, $ids_etablissements_enfants = null)
    {
        $service_etablissement = new Service_Etablissement;
        $defaults_values = $service_etablissement->getDefaultValues($genre, $numinsee, $type, $categorie, $local_sommeil, $classe, $id_etablissement_pere, $ids_etablissements_enfants);
        return $defaults_values;
    }
}