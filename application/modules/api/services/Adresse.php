<?php

class Api_Service_Adresse
{
    /**
     * Récupération des communes via le nom ou le code postal
     *
     * @param string $q Code postal ou nom d'une commune
     * @return string
     */
    public function get($q)
    {
        $service_adresse = new Service_Adresse;
        return $service_adresse->get($q);
    }
    
    /**
     * Retourne les types de voie d'une commune identifiée par son code insee
     *
     * @param int $code_insee
     * @return string
     */
    public function getTypesVoieParVille($code_insee)
    {
        $service_adresse = new Service_Adresse;
        return $service_adresse->getTypesVoieByVille($code_insee);
    }

    /**
     * Retourne les voies par rapport à une ville
     *
     * @param int $code_insee
     * @param string $q
     * @return string
     */
    public function getVoies($code_insee, $q = '')
    {
        $service_adresse = new Service_Adresse;
        return $service_adresse->getVoies($code_insee, $q);
    }
}