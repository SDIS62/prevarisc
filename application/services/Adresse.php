<?php

class Service_Adresse
{
    /**
     * Récupération de l'ensemble des communes
     *
     * @return array
     */
    public function getAllCommunes()
    {
    	$model_commune = new Model_DbTable_AdresseCommune;
    	return $model_commune->fetchAll()->toArray();
    }

    /**
     * Récupération des communes via le nom ou le code postal
     *
     * @param string $q Code postal ou nom d'une commune
     * @return array
     */
    public function get($q)
    {
        if(strlen($q) == 5 && is_numeric($q)) {
            $DB_adresse = new Model_DbTable_EtablissementAdresse;
            return $DB_adresse->getVilleByCP($q);
        }

        $model_adresse = new Model_DbTable_AdresseCommune;
        return $model_adresse->get($q);
    }
    
    /**
     * Retourne les types de voie d'une commune identifiée par son code insee
     *
     * @param int $code_insee
     * @return array
     */
    public function getTypesVoieParVille($code_insee)
    {
        $DB_adresse = new Model_DbTable_EtablissementAdresse;
        return $DB_adresse->getTypesVoieByVille($code_insee);
    }

    /**
     * Retourne les voies par rapport à une ville
     *
     * @param int $code_insee
     * @param string $q
     * @return array
     */
    public function getVoies($code_insee, $q = '')
    {
        $DB_adresse = new Model_DbTable_EtablissementAdresse;
        return $DB_adresse->getVoies($code_insee, $q);
    }
}
