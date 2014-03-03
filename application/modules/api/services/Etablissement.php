<?php

class Api_Service_Etablissement
{
    /**
     * Retourne un seul établissement identifié par le paramètre id.
     *
     * @param int id
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
     * @param int id
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
     * @param int id
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
     * @param int id
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
     * @param int id
     * @return string
     */
	public function getPiecesJointes($id)
	{
		$service_etablissement = new Service_Etablissement;
		$pieces_jointes = $service_etablissement->getAllPJ($id);
        return $pieces_jointes;
    }

    /**
     * Retourne lles contacts d'un établissement identifié par le paramètre id.
     *
     * @param int id
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
     * @param int id
     * @return string
     */
	public function getDossiers($id)
	{
		$service_etablissement = new Service_Etablissement;
		$dossiers = $service_etablissement->getDossiers($id);
        return $dossiers;
    }
}